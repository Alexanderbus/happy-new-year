class Popup {
    constructor(popup) {
        this._popup = popup;
        this._handleEscClose = this._handleEscClose.bind(this);
    }

    open() {
        this._popup.classList.add('popup_opened')
        document.addEventListener('keydown', this._handleEscClose)
    }

    close() {
        this._popup.classList.remove('popup_opened')
        document.removeEventListener('keydown', this._handleEscClose)
    }

    _handleEscClose(event) {
        if (event.key === 'Escape') {
            this.close();
        }
    }

    setEventListeners() {
        this._popup.addEventListener('click', (event) => {
            if (event.target.classList.contains('popup') || event.target.classList.contains('popup__exit')) {
                this.close();
            }
        });
    }
}
class PopupWithForm extends Popup {
    constructor(popup, handleSubmit) {
        super(popup);
        this._handleSubmit = handleSubmit;
        this._popupForm = this._popup.querySelector('.popup__form')
        this._inputList = this._popup.querySelectorAll('.popup__input') // я передалал инпут, но вы же сказали просто что нужно в this добавлять эелменты которые используются несколько раз,  а этот элемент используется только один раз
    }

    getInputValues() {
        const formValues = {};
        this._inputList.forEach(input => {
            formValues[input.name] = input.value;
        });
        return formValues;
    }

    setEventListeners() {
        super.setEventListeners();
        this._popupForm.addEventListener('submit', (event) => {
            event.preventDefault();
            this._handleSubmit(this.getInputValues());
        });
    }

    close() {
        super.close();
        this._popupForm.reset();
    }
}
class FormValidator {
    constructor(config, form) {
        this._errorClass = config.errorClass;  // popup__input_invalid 
        this._disableButton = config.disableButton; // popup__submit-button_disabled
        this._input = config.input; // popup__input
        this._form = form;
        this._submitButton = form.querySelector(config.submitButton)
        this._inputList = form.querySelectorAll(config.input)
    }

    setInputValidState(input, errorElement) {
        input.classList.remove(this._errorClass)
        errorElement.textContent = ''
    }

    _setInputInvalidState(input, errorElement) {
        input.classList.add(this._errorClass)
        errorElement.textContent = input.validationMessage
    }

    resetError() {
        this._inputList.forEach(input => {
            const errorElement = this._form.querySelector(`#${input.id}Error`)
            this.setInputValidState(input, errorElement)
        })
    }

    _checkInputValidity(input) {
        const errorElement = this._form.querySelector(`#${input.id}Error`)
        if (input.checkValidity()) {
            this.setInputValidState(input, errorElement)
        }
        else {
            this._setInputInvalidState(input, errorElement)
        }
    }

    disableButton() {
        this._submitButton.setAttribute('disabled', '')
        this._submitButton.classList.add(this._disableButton);
    }

    _enableButton() {
        this._submitButton.removeAttribute('disabled')
        this._submitButton.classList.remove(this._disableButton);
    }

    _toggleButtonValidity() {
        if (this._form.checkValidity()) {
            this._enableButton()
        }
        else {
            this.disableButton()
        }
    }

    enableValidation() {
        this._inputList.forEach((input) => {
            input.addEventListener('input', () => {
                this._checkInputValidity(input)
                this._toggleButtonValidity()
            })
        })
    }
}

const config = {
    errorClass: 'popup__input_invalid',
    disableButton: 'popup__submit-button_disabled',
    submitButton: '.popup__submit-button',
    input: '.popup__input',
};

const buttonform = document.querySelector('.gallery__button');
const popup1 = document.querySelector('.popup')
const popupForm = popup1.querySelector('.popup__form')

function test() {
    console.log('test')
}

const validation = new FormValidator(config, popupForm)

async function formSubmit() {
    const data = popup.getInputValues(); // получаем данные формы
    const response = await sendData(data); // отправляем данные на почту
    if (response.ok) {
        let result = await response.json(); // если ответ OK отвечает пользователю
        alert(result.message); // .. что данные отправлены
        validation.resetError()
    } else {
        alert("Код ошибки: " + response.status); // если not OK - показываем код ошибки
    }
}

async function sendData(data) {
    return await fetch("send_mail.php", {
        // отправляем в скрипт send_mail.php
        method: "POST", // методом POST
        body: data,
    });
}

const popup = new PopupWithForm(popup1, () => {
    formSubmit()
    popup.close();
})

buttonform.addEventListener('click', () => {
    popup.open()
    validation.disableButton()
})

function launchValidation(form) {
    form.enableValidation()
}

launchValidation(validation)

popup.setEventListeners()

