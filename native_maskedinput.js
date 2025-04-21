class PhoneMask {
  constructor(selector = "input[type='tel']", options = {}) {
    this.inputs = document.querySelectorAll(selector);

    // Опции по умолчанию
    this.options = Object.assign({
      pattern: '+375(__)___-__-__',
      placeholderChar: '_',
      prefix: '+375',
      autofillFrom: null, // 'localStorage' | 'sessionStorage' | null
    }, options);

    this.init();
  }

  init() {
    this.inputs.forEach((input) => {
      if (this.options.autofillFrom && this.getStoredPhone()) {
        input.value = this.formatValue(this.getDigits(this.getStoredPhone()));
      }

      input.addEventListener('focus', () => this.onFocus(input));
      input.addEventListener('blur', () => this.onBlur(input));
      input.addEventListener('input', (e) => this.onInput(e, input));
      input.addEventListener('keydown', (e) => this.onKeyDown(e, input));
    });
  }

  getStoredPhone() {
    try {
      if (this.options.autofillFrom === 'localStorage') {
        return localStorage.getItem('phone') || '';
      }
      if (this.options.autofillFrom === 'sessionStorage') {
        return sessionStorage.getItem('phone') || '';
      }
    } catch (e) {
      return '';
    }
  }

  getDigits(value) {
    return value.replace(/\D/g, '').replace(this.options.prefix.replace(/\D/g, ''), '').slice(0, 9);
  }

  formatValue(digits) {
    const placeholderChar = this.options.placeholderChar;
    const prefix = this.options.prefix;
    let result = prefix;
    if (digits.length > 0) result += '(' + digits.slice(0, 2);
    if (digits.length >= 2) result += ')';
    if (digits.length >= 3) result += digits.slice(2, 5);
    if (digits.length >= 5) result += '-' + digits.slice(5, 7);
    if (digits.length >= 7) result += '-' + digits.slice(7, 9);

    const filledLength = result.length;
    return result + placeholderChar.repeat(this.options.pattern.length - filledLength);
  }

  setCursorPosition(input, pos) {
    input.setSelectionRange(pos, pos);
  }

  onFocus(input) {
    if (input.value.trim() === '') {
      input.value = this.options.pattern;
    }
    setTimeout(() => {
      const firstEmpty = input.value.indexOf(this.options.placeholderChar);
      if (firstEmpty !== -1) this.setCursorPosition(input, firstEmpty);
    }, 0);
  }

  onBlur(input) {
    if (this.getDigits(input.value).length < 9) {
      input.value = '';
    } else {
      this.savePhone(input.value);
    }
  }

  onInput(e, input) {
    const digits = this.getDigits(input.value);
    const formatted = this.formatValue(digits);
    input.value = formatted;

    const next = formatted.indexOf(this.options.placeholderChar);
    this.setCursorPosition(input, next !== -1 ? next : formatted.length);
  }

  onKeyDown(e, input) {
    const pos = input.selectionStart;
    const digits = this.getDigits(input.value);

    if (e.key === 'Backspace' && pos > this.options.prefix.length) {
      e.preventDefault();
      const newDigits = digits.slice(0, -1);
      const formatted = this.formatValue(newDigits);
      input.value = formatted;

      const next = formatted.indexOf(this.options.placeholderChar);
      this.setCursorPosition(input, next !== -1 ? next : formatted.length);
    }

    const allowed = ['ArrowLeft', 'ArrowRight', 'Backspace', 'Delete', 'Tab'];
    if (!/\d/.test(e.key) && !allowed.includes(e.key)) {
      e.preventDefault();
    }
  }

  savePhone(phoneValue) {
    const digits = this.getDigits(phoneValue);
    if (digits.length === 9) {
      const fullPhone = this.formatValue(digits);
      if (this.options.autofillFrom === 'localStorage') {
        localStorage.setItem('phone', fullPhone);
      }
      if (this.options.autofillFrom === 'sessionStorage') {
        sessionStorage.setItem('phone', fullPhone);
      }
    }
  }
}

