(function ($) {
    var WidgetElements_PdfViewer = function ($scope, $) {
		let scope = $scope[0];
        var elementSettings = dceGetElementSettings($scope);
		var url = '';

		if ( 'url' === elementSettings.source && elementSettings.source_url.url ) {
			url = elementSettings.source_url.url;
        } else if( 'media_file' === elementSettings.source && elementSettings.source_media ) {
			url = elementSettings.source_media;
		}
		if( ! url ){
			return;
		}

		// Loaded via <script> tag, create shortcut to access PDF.js exports.
		var pdfjsLib = window['pdfjs-dist/build/pdf'];

		// The workerSrc property shall be specified.
		pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.9.359/pdf.worker.min.js';

		// Following code adapted from: https://code.tutsplus.com/tutorials/how-to-create-a-pdf-viewer-in-javascript--cms-32505
		var myState = {
			pdf: null,
			currentPage: 1,
			zoom: 1
		}

		pdfjsLib.getDocument(url).promise.then((pdf) => {
			myState.pdf = pdf;
			myState.zoom = elementSettings.zoom;
			render();
		});

		if ('yes' === elementSettings.download_controls) {
			let download_button = scope.getElementsByClassName("dce-pdf-download")[0];
			download_button.addEventListener("click", (e) => {
				var link = document.createElement("a");
				link.href = url.url;
				link.download = url.url;
				link.style.display = "none";
				document.body.appendChild(link);
				link.click();
				document.body.removeChild(link);
				render();
			});
		}

		if ('yes' === elementSettings.print_controls) {
			let download_button = scope.getElementsByClassName("dce-pdf-print")[0];
			download_button.addEventListener("click", (e) => {
				window.print();
				render();
			});
		}

		if ( 'yes' === elementSettings.navigation_controls ) {
			scope.getElementsByClassName('dce-pdf-go-previous')[0].addEventListener('click', (e) => {
				if(myState.pdf == null || myState.currentPage == 1)
					return;
				myState.currentPage -= 1;
				scope.getElementsByClassName("dce-pdf-current-page")[0].value = myState.currentPage;
				render();
			});

			scope.getElementsByClassName('dce-pdf-go-next')[0].addEventListener('click', (e) => {
				if(myState.pdf == null || myState.currentPage === myState.pdf.numPages)
					return;
				myState.currentPage += 1;
				scope.getElementsByClassName("dce-pdf-current-page")[0].value = myState.currentPage;
				render();
			});

			scope.getElementsByClassName('dce-pdf-current-page')[0].addEventListener('keypress', (e) => {
				if(myState.pdf == null) return;

				// Get key code
				var code = (e.keyCode ? e.keyCode : e.which);

				// If key code matches that of the Enter key
				if(code == 13) {
					var desiredPage =
						scope.getElementsByClassName('dce-pdf-current-page')[0].valueAsNumber;

					if(desiredPage >= 1 && desiredPage <= myState.pdf._pdfInfo.numPages) {
						myState.currentPage = desiredPage;
						scope.getElementsByClassName("dce-pdf-current-page")[0].value = desiredPage;
						render();
					}
				}
			});
		}

		if ( 'yes' === elementSettings.zoom_controls ) {
			scope.getElementsByClassName('dce-pdf-zoom-in')[0].addEventListener('click', (e) => {
				if(myState.pdf == null) return;
				myState.zoom += 0.5;
				render();
			});

			scope.getElementsByClassName('dce-pdf-zoom-out')[0].addEventListener('click', (e) => {
				if(myState.pdf == null) return;
				myState.zoom -= 0.5;
				render();
			});
		}

        function render() {
            myState.pdf.getPage(myState.currentPage).then((page) => {

                var canvas = scope.getElementsByClassName("dce-pdf-renderer")[0];
                var ctx = canvas.getContext('2d');

                var viewport = page.getViewport({scale: myState.zoom});

				if ('yes' === elementSettings.size_adjustable_controls) {
					// Calculate the new size of the PDF viewer
					const newWidth = elementSettings.size_adjustable_width; 
					const newHeight = elementSettings.size_adjustable_height; 
					// change viewport global size
					 viewport.width = newWidth;
					 viewport.height = newHeight;
				}

                canvas.width = viewport.width;
                canvas.height = viewport.height;

                page.render({
                    canvasContext: ctx,
                    viewport: viewport
                });
            });
        }
	};

    // Make sure you run this code under Elementor..
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/dce-pdf-viewer.default', WidgetElements_PdfViewer);
    });
})(jQuery);
