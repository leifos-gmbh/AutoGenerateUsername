// inserts placeholder at current coursor position
function insertTextIntoTextField(text, obj_name)
{
    if (text && obj_name)
    {
		var objTextField = document.getElementsByName(obj_name)[0];

		console.log(objTextField);
		if (document.selection)
		{
        	objTextField.focus();
            sel = document.selection.createRange();
            sel.text = text;
        }
        else if (objTextField.selectionStart || objTextField.selectionStart == '0')
        {
            var startPos = objTextField.selectionStart;
            var endPos = objTextField.selectionEnd;
            var TextFieldValue = objTextField.value;

            objTextField.value = TextFieldValue.substring(0, startPos) +
	    				text +
					TextFieldValue.substring(endPos, TextFieldValue.length);
        }
        else
        {
            objTextField.value += text;
        }
    }
}