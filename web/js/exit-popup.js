    (function() {
        setTimeout(function() {
            var __redirect_to = 'http://localhost:8000/';//

            var _tags = ['button', 'input', 'a'], _els, _i, _i2;
            for(_i in _tags) {
                _els = document.getElementsByTagName(_tags[_i]);
                for(_i2 in _els) {
                    if((_tags[_i] == 'input' && _els[_i2].type != 'button' && _els[_i2].type != 'submit' && _els[_i2].type != 'image') || _els[_i2].target == '_blank') continue;
                    _els[_i2].onclick = function() {window.onbeforeunload = function(){};}
                }
            }

            window.onbeforeunload = function() {
                setTimeout(function() {
                    window.onbeforeunload = function() {};
                    setTimeout(function() {
                        document.location.href = __redirect_to;
                    }, 500);
                },5);
                return 'Вы действительно хотите покинуть сайт?';
            }
        }, 500);
    })();