(function (document) {
  var form = document.getElementById('form'),
    input = document.getElementById('input'),
    indent = document.getElementById('indent'),
    output = document.getElementById('output'),
    error = document.getElementById('error'),
    indentValue = 2,
    CLASS_MAPPINGS = {
      'null': 'n0',
      'string': 's',
      'number': 'n',
      'boolean': 'b'
    },
    regexModifiers = 'gmi';

  form.addEventListener("submit", processJSON);
  
  /**
   * Form submit handler to process the input JSON.
   */
  function processJSON(event) {
    var inputJSON = input.value,
      formattedJSON;
    
    event.preventDefault();
    
    if (inputJSON) {
      try {
        indentValue = parseInt(indent.options[indent.selectedIndex].value, 10);
        error.innerHTML = '';
        inputJSON = JSON.parse(inputJSON);
        formattedJSON = JSON.stringify(inputJSON, null, indentValue);
        input.value = formattedJSON;
        output.innerHTML = syntaxHighlight(formattedJSON);
        output.focus();
      } catch (err) {
        error.innerHTML = err.message;
        error.focus();
      }
    }
  }
  
  /**
   * Highlight the JSON string
   */
  function syntaxHighlight(json) {
    // highlight object property keys
    json = json.replace(/^(\s+".*?"):/gmi, "<span class='p'>$1</span>:");

    // highlight null values
    json = highlightByType(json, 'null', 'null');
    // highlight string values
    json = highlightByType(json, 'string', '".*?"');
    // highlight number values
    json = highlightByType(json, 'number', '[0-9.]*?');
    // highlight boolean values
    json = highlightByType(json, 'boolean', '(true|false)');

    return json;
  }

  /**
   * Highlight object values by type.
   * 
   * Handle values in the following format:
   * @example
   * {
   *   "name": "value",
   *   "names": [
   *     "value1",
   *     "value2"
   *   ]
   * }
   *
   * @param {String} input - input string
   * @param {String} type - of variable (string|number|boolean|null)
   * @param {String} matcher - regular expression string to match the type.
   *
   * @returns {String}
   */
  function highlightByType(input, type, matcher) {
    var cssClass = CLASS_MAPPINGS[type] || '';

    input = input.replace(new RegExp('^(\\s+' + matcher + ')$', regexModifiers), '<span class="' + cssClass + '">$1</span>');
    input = input.replace(new RegExp('^(\\s+' + matcher + '),$', regexModifiers), '<span class="' + cssClass + '">$1</span>,');
    input = input.replace(new RegExp(': (' + matcher + '),$', regexModifiers), ': <span class="' + cssClass + '">$1</span>,');
    input = input.replace(new RegExp(': (' + matcher + ')$', regexModifiers), ': <span class="' + cssClass + '">$1</span>');

    return input;
  }
})(document);