/**
 * Function: Show No Iamge if entity image not exist
 *
 * @param     object     obj
 * @param     string     errorImg
 * @return    (Set Image src)     
 */
function onErrorImage(obj, errorImg) {
	if(obj.src != errorImg){
		obj.src = errorImg;
    } 
}
/**
 * Function: Get Formatted title
 *
 * @param     string     word
 * @return    string     word     
 */
function getFormatTitle(word){
	var word_cnt = word.split(' ');
	var cnt = word_cnt.length;
	var firsthalf = word_cnt.slice(0,cnt/2);
	var secondhalf = word_cnt.slice(cnt/2);
	if(cnt > 1){
		var title_format = " <span>"+firsthalf.join(' ')+" </span>"+secondhalf.join(' ');
	}else{
		var title_format = " <span>"+word+" </span>";
	}
	
	return title_format;
}

/**
 * Function: Set Message for modal
 *
 * @param     string     word
 * @return    string     word     
 */
 function displayMessageBox(msg) {
    var html = ``;

    html = `<div class="modal-header white">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>${msg}</strong></p>
            </div>
            <div class="modal-footer">
				<button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Ok</button>
            </div>`; 
    setTimeout(function () {
        $('#btn_ok').focus();
    }, 1000);
    return html;
}

/**
 * Function: Set Message for modal
 *
 * @param     string     word
 * @return    string     word     
 */
 function displayVideoBox(url) {
    var html = ``;

    html = `<div class="modal-header white">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               ${url}
            </div>
            <div class="modal-footer"></div>`; 
    setTimeout(function () {
        $('#btn_ok').focus();
    }, 1000);
    return html;
}

function make_slug(word, separator){
	if(separator === undefined){
		separator = '-';
	}
	word = word.toLowerCase();
	// Convert whitespaces and underscore to the given separator
    string = word.replace(/ /g, separator);
	return string;
}

function formatPrice(price) {
  return currency_symbol+' '+price.toFixed(2).replace(/./g, function(c, i, a) {
    return i > 0 && c !== "." && (a.length - i) % 3 === 0 ? "," + c : c;
  });
}