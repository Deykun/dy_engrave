function documentLoaded(cb) {
  if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading"){
    cb();
  } else {
    document.addEventListener('DOMContentLoaded', cb);
  }
}

documentLoaded( function(e) {
	if (document.getElementById('add-engrave') !== null) {
		
		var dyEngrave = {};
		
		dyEngrave.elForm = document.getElementById('form-engrave');
		dyEngrave.elPrice = document.getElementById('price-engrave');
		dyEngrave.elDatalist = document.getElementById('impact-engrave');	
		
		dyEngrave.elText = document.getElementById('text-engrave');
		dyEngrave.elProduct = document.getElementById('text-engrave');
		
		dyEngrave.basePrice = Number( dyEngrave.elPrice.getAttribute('data-base') );
		dyEngrave.impactPrice = [];
		
		Array.prototype.forEach.call( dyEngrave.elDatalist.querySelectorAll('li') , function(el, i) {
			var length = Number( el.getAttribute('data-length') );
			var impact = Number( el.getAttribute('data-impact') );
			dyEngrave.impactPrice[length] = impact;
		});
		
		
		dyEngrave.updateText = function(e) {
			var text = e.target.value;
			dyEngrave.elPrice.innerHTML = (dyEngrave.basePrice + dyEngrave.impactPrice[text.length]).toFixed(2);
		}
		
		console.dir(dyEngrave);
		
		dyEngrave.elText.addEventListener('input', dyEngrave.updateText);
		
		document.getElementById('add-engrave').addEventListener('click', function(e) {
			var form = dyEngrave.elForm.getAttribute('class');
			
			if (form == 'close') {
				dyEngrave.elForm.setAttribute('class', 'open');
			} else {
				dyEngrave.elForm.setAttribute('class', 'close');
			}
		});
		
//		dyEngrave.elForm.addEventListener('click', function(e) {
//			var form = dyEngrave.elForm.getAttribute('class');
//			
//			if (form == 'close') {
//				dyEngrave.elForm.setAttribute('class', 'open');
//			} else {
//				dyEngrave.elForm.setAttribute('class', 'close');
//			}
//		});
//		
	}
});