class InputMask {
  constructor(input, mask, options = {}) {
    this.input = input;
    this.mask = mask;
    this.options = {
      placeholder: '_',
      definitions: {
        '9': '[0-9]',
        'a': '[A-Za-z]',
        '*': '[A-Za-z0-9]'
      },
      ...options
    };
    
    this.maskPattern = this._createMaskPattern();
    this._setupEvents();
    this._applyInitialMask();
  }

  _createMaskPattern() {
    const pattern = [];
    const definitions = this.options.definitions;
    
    this.mask.split('').forEach((char, index) => {
      if (definitions[char]) {
        pattern.push({
          index,
          regex: new RegExp(definitions[char]),
          placeholder: this.options.placeholder.charAt(index) || this.options.placeholder
        });
      }
    });
    
    return pattern;
  }

  _setupEvents() {
    this.input.addEventListener('focus', this._handleFocus.bind(this));
    this.input.addEventListener('blur', this._handleBlur.bind(this));
    this.input.addEventListener('input', this._handleInput.bind(this));
    this.input.addEventListener('keydown', this._handleKeyDown.bind(this));
  }

  _applyInitialMask() {
    if (!this.input.value) {
      let maskedValue = '';
      let maskIndex = 0;
      
      for (let i = 0; i < this.mask.length; i++) {
        const isPatternChar = this.maskPattern.some(p => p.index === i);
        maskedValue += isPatternChar ? this.options.placeholder : this.mask[i];
      }
      
      this.input.value = maskedValue;
    }
  }

  _handleFocus() {
    if (this.input.value === '') {
      this._applyInitialMask();
    }
    this._setCaretPosition(this._getFirstEditablePosition());
  }

  _handleBlur() {
    if (this.input.value === this._getEmptyMask()) {
      this.input.value = '';
    }
  }

  _handleInput(e) {
    const value = this.input.value;
    const newValue = this._applyMask(value);
    
    if (value !== newValue) {
      this.input.value = newValue;
    }
    
    // Сохраняем позицию курсора
    const caretPos = this.input.selectionStart;
    setTimeout(() => {
      this._setCaretPosition(this._getNextEditablePosition(caretPos - 1));
    }, 0);
  }

  _handleKeyDown(e) {
    // Обработка Backspace и Delete
    if (e.key === 'Backspace' || e.key === 'Delete') {
      const caretPos = this.input.selectionStart;
      const selectionLength = this.input.selectionEnd - caretPos;
      
      if (selectionLength > 0) {
        // Удаление выделенного текста
        e.preventDefault();
        this._clearSelection(caretPos, caretPos + selectionLength);
      } else if (e.key === 'Backspace' && caretPos > 0) {
        // Обработка Backspace
        e.preventDefault();
        const prevPos = this._getPreviousEditablePosition(caretPos - 1);
        if (prevPos !== -1) {
          this._clearCharacter(prevPos);
          this._setCaretPosition(prevPos);
        }
      } else if (e.key === 'Delete' && caretPos < this.input.value.length) {
        // Обработка Delete
        e.preventDefault();
        const nextPos = this._getNextEditablePosition(caretPos);
        if (nextPos !== -1) {
          this._clearCharacter(nextPos);
          this._setCaretPosition(nextPos);
        }
      }
    }
  }

  _applyMask(value) {
    let result = this._getEmptyMask();
    let valueIndex = 0;
    
    for (let i = 0; i < this.mask.length && valueIndex < value.length; i++) {
      const pattern = this.maskPattern.find(p => p.index === i);
      
      if (pattern) {
        const char = value[valueIndex];
        if (pattern.regex.test(char)) {
          result = this._setCharAt(result, i, char);
          valueIndex++;
        }
      } else {
        if (value[valueIndex] === this.mask[i]) {
          valueIndex++;
        }
      }
    }
    
    return result;
  }

  _clearSelection(start, end) {
    let value = this.input.value.split('');
    for (let i = start; i < end; i++) {
      const pattern = this.maskPattern.find(p => p.index === i);
      if (pattern) {
        value[i] = pattern.placeholder;
      }
    }
    this.input.value = value.join('');
  }

  _clearCharacter(pos) {
    const pattern = this.maskPattern.find(p => p.index === pos);
    if (pattern) {
      let value = this.input.value.split('');
      value[pos] = pattern.placeholder;
      this.input.value = value.join('');
    }
  }

  _getEmptyMask() {
    let result = '';
    for (let i = 0; i < this.mask.length; i++) {
      const pattern = this.maskPattern.find(p => p.index === i);
      result += pattern ? pattern.placeholder : this.mask[i];
    }
    return result;
  }

  _getFirstEditablePosition() {
    const firstPattern = this.maskPattern[0];
    return firstPattern ? firstPattern.index : 0;
  }

  _getNextEditablePosition(currentPos) {
    for (let i = currentPos + 1; i < this.mask.length; i++) {
      if (this.maskPattern.some(p => p.index === i)) {
        return i;
      }
    }
    return -1;
  }

  _getPreviousEditablePosition(currentPos) {
    for (let i = currentPos; i >= 0; i--) {
      if (this.maskPattern.some(p => p.index === i)) {
        return i;
      }
    }
    return -1;
  }

  _setCharAt(str, index, char) {
    return str.substring(0, index) + char + str.substring(index + 1);
  }

  _setCaretPosition(pos) {
    this.input.setSelectionRange(pos, pos);
  }

  // Публичные методы
  unmask() {
    let unmaskedValue = '';
    const value = this.input.value;
    
    this.maskPattern.forEach(pattern => {
      const char = value[pattern.index];
      if (char !== pattern.placeholder) {
        unmaskedValue += char;
      }
    });
    
    return unmaskedValue;
  }

  destroy() {
    this.input.removeEventListener('focus', this._handleFocus);
    this.input.removeEventListener('blur', this._handleBlur);
    this.input.removeEventListener('input', this._handleInput);
    this.input.removeEventListener('keydown', this._handleKeyDown);
  }
}

// Использование:
document.addEventListener('DOMContentLoaded', function() {
  const phoneInput = document.querySelector('.InputPhone');
  new InputMask(phoneInput, '+7 (999) 999-99-99', {
    placeholder: '_'
  });
});
