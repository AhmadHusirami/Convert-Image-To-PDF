<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image To PDF Generator</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/logo.6e0ed5db.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/index.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
</head>
<body>
    <div id="root">
        <nav class="sticky-top navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <i class="fa fa-image"></i>
                <img src="{{ asset('assets/logo.6e0ed5db.png') }}" class="nav-logo" alt="nav-logo">
                <p class="container-text">Converter Image To PDF</p>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="sidebar">
                <button id="addImageBtn"><i class="fa fa-image"></i> Add Image</button>
                <input type="file" id="imageInput" accept=".png,.jpg,.jpeg" style="display: none;">
                <div class="paper-size-container">
                    <label for="paperSize">Paper size</label>
                    <select id="paperSize">
                        <option value="A4">A4</option>
                        <option value="Letter">Letter</option>
                        <option value="Legal">Legal</option>
                        <option value="Tabloid">Tabloid</option>
                        <option value="Executive">Executive</option>
                    </select>
                </div>

                <div class="paper-size-container">
                    <label for="marginSize">Margin Size</label>
                    <select id="marginSize">
                        <option value="no-margin">No Margin</option>
                        <option value="normal">Normal</option>
                        <option value="narrow">Narrow</option>
                        <option value="moderate">Moderate</option>
                    </select>
                </div>

                <div class="paper-size-container">
                    <label for="imageFit">Image Fit</label>
                    <select id="imageFit">
                        <option value="top">Image Top</option>
                        <option value="center">Image Center</option>
                        <option value="bottom">Image Bottom</option>
                        <option value="cover">Image Cover</option>
                        <option value="stretch">Image Stretch</option>
                    </select>
                </div>

                <button id="createPdfBtn"><i class="fa-sharp fa-regular fa-file-pdf"></i> Create PDF</button>

                <footer>
                    <p class="footer">© Copyright <script>document.write(new Date().getFullYear())</script> <a class="footer_name" href="https://ahmad-husirami.vercel.app/" target="_blank"> Ahmad Husirami</a></p>
                </footer>
            </div>
        </div>

        <div class="content">
            <div id="pdfContainer" style="width: 100%; height: 100%;">
                <img id="selectedImage" style="max-width: 100%; display: none;" alt="Selected Image">
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <script>
        document.getElementById('addImageBtn').addEventListener('click', function() {
            document.getElementById('imageInput').click();
        });

        document.getElementById('imageInput').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file && !['image/png', 'image/jpeg'].includes(file.type)) {
                alertify.error('<i class="fa fa-exclamation-circle"></i> Error: Only PNG/JPG files are allowed.');
                event.target.value = '';
            } else if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('selectedImage');
                    img.src = e.target.result;
                    img.style.display = 'block';
                    updateImageDisplay();
                }
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('paperSize').addEventListener('change', updateImageDisplay);
        document.getElementById('marginSize').addEventListener('change', updateImageDisplay);
        document.getElementById('imageFit').addEventListener('change', updateImageDisplay);

        function updateImageDisplay() {
            const paperSize = document.getElementById('paperSize').value;
            const marginSize = document.getElementById('marginSize').value;
            const imageFit = document.getElementById('imageFit').value;
            const img = document.getElementById('selectedImage');

            const pdfContainer = document.getElementById('pdfContainer');

            pdfContainer.style.width = getPaperWidth(paperSize);
            pdfContainer.style.height = getPaperHeight(paperSize);
            pdfContainer.style.padding = getMarginSize(marginSize);
            img.style.objectFit = getImageFit(imageFit);
        }

        function getPaperWidth(paperSize) {
            switch (paperSize) {
                case 'A4': return '210mm';
                case 'Letter': return '216mm';
                case 'Legal': return '216mm';
                case 'Tabloid': return '279mm';
                case 'Executive': return '184mm';
                default: return '210mm';
            }
        }

        function getPaperHeight(paperSize) {
            switch (paperSize) {
                case 'A4': return '297mm';
                case 'Letter': return '279mm';
                case 'Legal': return '356mm';
                case 'Tabloid': return '432mm';
                case 'Executive': return '267mm';
                default: return '297mm';
            }
        }

        function getMarginSize(marginSize) {
            switch (marginSize) {
                case 'no-margin': return '0';
                case 'normal': return '1cm';
                case 'narrow': return '0.5cm';
                case 'moderate': return '2cm';
                default: return '1cm';
            }
        }

        function getImageFit(imageFit) {
            switch (imageFit) {
                case 'top': return 'none';
                case 'center': return 'contain';
                case 'bottom': return 'none';
                case 'cover': return 'cover';
                case 'stretch': return 'fill';
                default: return 'contain';
            }
        }

        document.getElementById('createPdfBtn').addEventListener('click', function() {
            const { jsPDF } = window.jspdf;
            const imgData = document.getElementById('selectedImage').src;
            const pdf = new jsPDF();
            pdf.addImage(imgData, 'JPEG', 10, 10, 180, 160);
            pdf.save("download.pdf");
        });
    </script>
</body>
</html>
