#!flask/bin/python
from flask import Flask, jsonify, request
import base64
from PIL import Image
import cv2
import io
import numpy as np
import pickle
import os
import keras.utils as image
from keras.models import load_model

app = Flask(__name__)
version="Face recogniser v1.0"

d = os.path.dirname(os.getcwd())
model_dir = os.path.join(d, "models/cnn_model_4.h5")

imported_model = load_model(model_dir, compile = False)

@app.route('/api/v1.0/version', methods=['GET'])
def get_data():
    #http://127.0.0.1:6878/api/v1.0/version
    if request.method == 'GET':
        return jsonify({'version': version})

@app.route('/api/v1.0/detect', methods = ['POST'])
def new_user():
    #http://127.0.0.1:6878/api/v1.0/detect
    if request.method == 'POST':
        test_image = request.form.getlist('image')[0].split(",")[1]
        imgdata = base64.b64decode(test_image)
        img = Image.open(io.BytesIO(imgdata))
        test_image= cv2.flip(cv2.cvtColor(np.array(img)[:, :, ::-1], cv2.COLOR_RGB2BGR), 1)
        test_image = Image.fromarray(test_image)

        width, height = test_image.size
        left = (width - 190) / 2
        top = (height - 190) / 2
        right = (width + 190) / 2
        bottom = (height + 190) / 2

        test_image = test_image.crop((left, top, right, bottom))
        test_image = np.array(test_image)

        x = image.img_to_array(test_image)
        x = np.expand_dims(x, axis=0)
        y = model.predict(x)[0]

        if y[0] > y[1]:
            return "false", 500, {'Access-Control-Allow-Origin': 'http://localhost:8888', 'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, OPTIONS', 'Access-Control-Allow-Headers': 'Content-Type'}
        else:
            return "true", 200, {'Access-Control-Allow-Origin': 'http://localhost:8888', 'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, OPTIONS', 'Access-Control-Allow-Headers': 'Content-Type'}

@app.after_request
def after_request(response):
    response.headers['Access-Control-Allow-Origin'] = 'http://localhost:8888'
    response.headers['Access-Control-Allow-Methods'] = 'GET, POST, PUT, DELETE, OPTIONS'
    response.headers['Access-Control-Allow-Headers'] = 'Content-Type'
    return response

if __name__ == '__main__':
    app.run(port=6878)