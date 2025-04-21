# webhat
web start 
new systems
# Пример использования native_maskedinput.js

<script>
  document.addEventListener('DOMContentLoaded', () => {
    new PhoneMask("input[type='tel']", {
      pattern: '+375(__)___-__-__',
      placeholderChar: '_',
      prefix: '+375',
      autofillFrom: 'localStorage'
    });
  });
</script>
