<div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="modelNameSelect" class="form-label">Select Model</label>
                    <select class="form-select" id="modelNameSelect">
                        <option value="pascal">Pascal</option>
                        <option value="cityscapes">City Scapes</option>
                        <option value="ade20k">ADE20K</option>
                    </select>
                </div>
                <button class="btn btn-primary" id="loadModel">Load Model</button>
                <p id="modelLoadedStatus" class="mt-2" style="color: mediumblue">Model not loaded..</p>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="chooseFiles" class="form-label">Choose Image</label>
                    <input type="file" class="form-control" id="chooseFiles" accept="image/*"/>
                </div>
                <button class="btn btn-primary" id="segmentImage" disabled>Segment Image</button>
                <button class="btn btn-primary" id="saveImage">Salva</button>
            </div>
        </div>
        
        <div class="container mt-5">
            <div class="col">
                <div id="imgWrapper" class="d-flex justify-content-center"></div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col">
                <label id="legendLabel" style="visibility: hidden;"></label>
                <div id="legends" style="visibility: hidden;"></div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col">
                <label id="removeOrRestoreSelectedObjectsLabel" style="visibility: hidden;">
                    Click one or more legends above to restore or remove the corresponding object from the input image
                </label>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col">
                <div id="buttonWrapper" style="visibility: hidden;">
                    <button class="btn btn-danger" id="removeSelectedObjects">Remove Selected Objects</button>
                    <button class="btn btn-success" id="restoreSelectedObjects">Restore Selected Objects</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const saveImageButton = document.getElementById("saveImage");

        saveImageButton.addEventListener("click", () => {
            const fileInput = document.getElementById('chooseFiles');
            const originalFile = fileInput.files[0];

            const modifiedCanvas = document.querySelector('canvas');
            const formData = new FormData();

            formData.append('imageFile', originalFile);

            modifiedCanvas.toBlob((blob) => {
                const segmentedImageFile = new File([blob], 'segmented_image.png');

                formData.append('segmentedImageFile', segmentedImageFile);

                fetch('save_image.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        alert("Immagini salvate con successo!");
                    } else {
                        alert("Si è verificato un errore durante il salvataggio delle immagini.");
                    }
                })
                .catch(error => {
                    console.error('Si è verificato un errore:', error);
                });
            }, 'image/png');
        });
    </script>

    <script src="https://unpkg.com/@tensorflow/tfjs-core@3.3.0/dist/tf-core.js"></script>
    <script src="https://unpkg.com/@tensorflow/tfjs-converter@3.3.0/dist/tf-converter.js"></script>
    <script src="https://unpkg.com/@tensorflow/tfjs-backend-webgl@3.3.0/dist/tf-backend-webgl.js"></script>
    <script src="https://unpkg.com/@tensorflow-models/deeplab@0.2.1/dist/deeplab.js"></script>
    
    