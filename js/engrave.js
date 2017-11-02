function documentLoaded(cb) {
  if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading"){
    cb();
  } else {
    document.addEventListener('DOMContentLoaded', cb);
  }
}

documentLoaded( function(e) {
	if (document.getElementById('add-engrave') !== null) {
		engraveForm = document.getElementById('form-engrave');
		
		document.getElementById('add-engrave').addEventListener('click', function(e) {
			var form = e.target.getAttribute('form');
			
			if (form == 'open') {
				engraveForm.style.display = 'none';
				e.target.setAttribute('form', '');
			} else {
				engraveForm.style.display = 'block';
				e.target.setAttribute('form', 'open');
			}
		});
		
	}
});