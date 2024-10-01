# Identity Verification in Moodle through Facial Recognition based on Deep Learning

<p align="center">
  <img src="assets/banner-light.png" alt="Logo" width="500">
</p>

## Description

This project aims to develop a **facial recognition**-based **identity verification** system for accessing the **Moodle** learning platform. It uses **convolutional neural networks** and **transfer learning** techniques to improve recognition accuracy and ensure academic integrity in remote exams and other sensitive activities within the platform.

## Features

- **Facial Recognition**: Based on convolutional neural network models.
- **Enhanced Security**: Implements two-factor authentication using facial recognition as a complement to the classic username and password authentication.
- **Privacy and Confidentiality**: Ensures data protection through encrypted cookies in the browser.
- **Moodle Plugin**: Facilitates the integration of the facial recognition system into the Moodle platform.

## Prerequisites

- Python 3.x
- Facial recognition library (`face_recognition`)
- Deep learning library (`tensorflow` or `keras`)
- Moodle installed and configured (minimum version 3.5)
- Docker server to run the plugin and API model

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/sergiocuencanunez/face-auth-moodle.git
cd face-auth-moodle
```

### 2. Set up a virtual environment

```bash
python -m venv venv
source venv/bin/activate  # On Linux or MacOS
venv\Scripts\activate     # On Windows
```

### 3. Install dependencies

```bash
pip install -r requirements.txt
```

### 4. Train the model

To train the facial recognition model using transfer learning based on the **VGGFace2** dataset:

```bash
python train_model.py --dataset path/to/VGGFace2 --output model/facial_recognition_model.h5
```

### 5. Run the API server

```bash
python run_api.py
```

### 6. Install the Moodle plugin

1. Copy the contents of the `moodle_plugin` folder into the plugin directory of your Moodle installation.
2. Configure the plugin to communicate with the facial recognition API server.

## Usage

### Access Moodle

1. Users enter their username and password on the Moodle login page.
2. The system prompts for a facial image capture, which is sent to the API server for verification.
3. If the facial image matches the user's identity, access is granted.

## Results

The system has demonstrated **100% accuracy** on test data thanks to the **transfer learning** technique employed. This ensures **security** and **confidentiality** when accessing the Moodle university platform.

<p align="center">
  <img src="assets/confusion_matrix.png" alt="Confusion Matrix" width="500">
</p>

## Contributions

This project is open to improvements. If you want to contribute:

1. Fork the repository.
2. Make your changes.
3. Submit a pull request with a clear description of the modifications.

## License

This project is licensed under the **Apache License 2.0**. See the [LICENSE](LICENSE) file for more details.

## References

1. Cook, S. (2023). [US schools leaked 32 million records in 2,691 data breaches since 2005](https://www.comparitech.com/blog/vpn-privacy/us-schools-data-breaches/). Comparitech.
2. Moodle - Open-source learning platform | Moodle.org. (n.d.). Retrieved from https://moodle.org/?lang=en.
3. Zhuang, F et al. (2019). [A Comprehensive Survey on Transfer Learning](https://ieeexplore.ieee.org/document/8682849). Proceedings of the IEEE.
