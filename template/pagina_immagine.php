<style>
    
    img {
        object-fit: cover; 
        border: 1px solid #ccc;
        border-radius: 5px;
        
    }

    .imageContainer {
        display: flex;
        justify-content: space-around;
    }

    .gradient-box {
        width: 50%;
        height: 100px; 
        border: 1px solid #ccc;
    }
</style>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tirocinio";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connessione al database fallita: " . $conn->connect_error);
}
?>

<form method="post" class="mt-4">
    <div class="input-group mb-3">
    <select class="form-select" name="segmentedImage" id="segmentedImageSelect">
        <?php
        $imageId = $_GET['id'];
        $sqlSegmentedImages = "SELECT ID, nome FROM segmented_images WHERE id_ImmagineOriginale = $imageId";
        $segmentedImagesResult = $conn->query($sqlSegmentedImages);

        if ($segmentedImagesResult->num_rows > 0) {
            while ($segmentedRow = $segmentedImagesResult->fetch_assoc()) {
                echo '<option value="' . $segmentedRow["nome"] . '"data-id="' . $segmentedRow["ID"] . '">' . $segmentedRow["nome"] . '</option>';
            }
        } else {
            echo '<option value="">Nessuna immagine segmentata disponibile</option>';
        }
        ?>
    </select>
    <label class="input-group-text">Seleziona Immagine Segmentata</label>
    </div>

    <div class="input-group mb-3">
        <select class="form-select" name="dataset">
            <?php
            $sql2 = "SELECT ID, Nome FROM dataset";
            $result2 = $conn->query($sql2);

            if ($result2->num_rows > 0) {
                while($row = $result2->fetch_assoc()) {
                    echo '<option value="' . $row["ID"] . '">' . $row["Nome"] . '</option>';
                    
                }
            } else {
                echo '<option value="">Nessun dataset disponibile</option>';
            }
            ?>
        </select>
        <label class="input-group-text">Seleziona Dataset</label>
    </div>
    <div class="input-group mb-3">
        <select class="form-select" name="gradientType">
            <option value="rightToLeft">Right to Left</option>
            <option value="leftToRight">Left to Right</option>
            <option value="concentric">Concentric</option>
        </select>
        <label class="input-group-text">Seleziona Tipo Gradiente</label>
    </div>

    <button type="submit" class="btn btn-primary">Genera Immagine</button>
</form>

<button id="saveButton" class="btn btn-success mt-3">Salva Immagine</button></br>

<div id="app" class="container mt-5">
    <form @submit.prevent="handleSubmit">
        <div class="row">
            <div class="col-md-6">
                <h2>Choose Palette</h2>
                <select v-model="selectedPalette" class="form-control">
                    <option v-for="(palette, index) in palettes" :value="index">{{ palette.name }}</option>
                </select>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <h2>Minimum Gradient</h2>
                <input type="number" v-model="minGradient" class="form-control">
            </div>
            <div class="col-md-6">
                <h2>Maximum Gradient</h2>
                <input type="number" v-model="maxGradient" class="form-control">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col">
                <h2>Gradient Preview</h2>
                <div class="gradient-box" :style="{ background: generatedGradients.leftToRight }"></div>
                <div class="gradient-box" :style="{ background: generatedGradients.rightToLeft }"></div>
                <div class="gradient-box" :style="{ background: generatedGradients.concentric }"></div>

            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Crea collegamento</button>
    </form>
</div>


<?php 
    if(isset($_GET['id'])) {
        $imageId = $_GET['id'];

        $sql = "SELECT nome FROM images WHERE ID = $imageId";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<img src="' . $row["nome"] . '" alt="Immagine" id="originalImage">';
            }
        } else {
            echo "Nessuna immagine trovata con l'ID specificato.";
        }
    } else {
        echo "ID immagine non specificato nella query string.";
    }
?>

<script>
    Vue.createApp({
        el: '#app',
        data() {
            return{
                selectedPalette: null,
                palettes: [],
                minGradient: 0,
                maxGradient: 100
            }
        },
        mounted() {
        this.fetchPalettes(); 
        },
        watch: {
            selectedPalette(newValue, oldValue) {
                this.fetchPalettes();
            }
        },
        computed: {
             generatedGradients() {
                if (this.selectedPalette !== null) {
                    const colors = this.palettes[this.selectedPalette].colors;
                    return {
                        leftToRight: `linear-gradient(to right, ${colors.join(', ')})`,
                        rightToLeft: `linear-gradient(to left, ${colors.join(', ')})`,
                        concentric: `radial-gradient(circle, ${colors.join(', ')})`
                    };
                } else {
                    return {
                        leftToRight: 'none',
                        rightToLeft: 'none',
                        concentric: 'none'
                    };
                }
            }
        },
            
        methods: {
            fetchPalettes() {
                axios.get('utils/palettes.php', {
                    params: {
                        minGradient: this.minGradient,
                        maxGradient: this.maxGradient
                    }
                })
                .then(response => {
                    this.palettes = response.data;
                })
                .catch(error => {
                    console.error('Error fetching palettes:', error);
                });
            },
          handleSubmit() {
            if (this.selectedPalette !== null) {
                const dataToSend = {
                    dataset: document.querySelector('select[name="dataset"]').value,
                    palette: this.palettes[this.selectedPalette].id,
                    minGradient: this.minGradient,
                    maxGradient: this.maxGradient
                };

                console.log(dataToSend);
                axios.post('utils/saveDatasetPalette.php', dataToSend)
                .then(response => {
                    console.log('Dati inviati con successo:', response.data);
                })
                .catch(error => {
                    console.error('Errore durante l\'invio dei dati:', error);
                });
            }
        },

          
        }
    }).mount('#app');
</script>


<script>
    let segmentedImage;
    let colors_length;
    originalImage.style.width = '400px';
    let selectedImageId;

    document.getElementById('segmentedImageSelect').addEventListener('change', function(event) {
    var selectedImage = event.target.value;
    selectedImageId = event.target.options[event.target.selectedIndex].getAttribute('data-id');
    console.log(selectedImageId);

    var image = document.createElement('img');
    segmentedImage = image;
    

    segmentedImage.onload = function() {
        var previousImages = document.querySelectorAll('.segmented-image-container');
        previousImages.forEach(function(node) {
            node.parentNode.removeChild(node);
        });

        var imageContainer = document.createElement('div');
        imageContainer.classList.add('segmented-image-container');
        imageContainer.style.display = 'inline';
        imageContainer.style.justifyContent = 'space-around';
        

        segmentedImage.style.width = '400px';

        imageContainer.appendChild(segmentedImage);
        

        document.body.appendChild(imageContainer);
    };

    segmentedImage.src = selectedImage;
});

    function generateGradient(colorsHex, gradientType) {
        var gradient = new cv.Mat(1, colorsHex.length, cv.CV_8UC4);
        switch (gradientType) {
            case "rightToLeft":
                gradient = new cv.Mat(1, colorsHex.length, cv.CV_8UC4);
                for (var i = 0; i < colorsHex.length; i++) {
                    var colorRgb = hexToRgba(colorsHex[i]);
                    gradient.data.set(colorRgb, i * 4);
                }
                break;

            case "leftToRight":
                gradient = new cv.Mat(1, colorsHex.length, cv.CV_8UC4);
                for (var i = 0; i < colorsHex.length; i++) {
                    var colorRgb = hexToRgba(colorsHex[i]);
                    gradient.data.set(colorRgb, (colorsHex.length - i - 1) * 4);
                }
                break;

            case "concentric":
                var center = [originalImage.width / 2, originalImage.height / 2];
                gradient = new cv.Mat(originalImage.height, originalImage.width, cv.CV_8UC4);
                for (var y = 0; y < originalImage.height; y++) {
                    for (var x = 0; x < originalImage.width; x++) {
                        var distanceToCenter = Math.sqrt(Math.pow(x - center[0], 2) + Math.pow(y - center[1], 2));
                        var colorIndex = Math.floor((distanceToCenter / Math.max(originalImage.width, originalImage.height)) * (colorsHex.length - 1));
                        var colorRgb = hexToRgba(colorsHex[colorIndex]);
                        var idx = y * originalImage.width * 4 + x * 4;
                        gradient.data[idx] = colorRgb[0];
                        gradient.data[idx + 1] = colorRgb[1];
                        gradient.data[idx + 2] = colorRgb[2];
                        gradient.data[idx + 3] = colorRgb[3];
                    }
                }
                break;
        }

        return gradient;
    }

    function hexToRgba(hex) {
        hex = hex.replace(/^#/, '');

        var r = parseInt(hex.substring(0, 2), 16);
        var g = parseInt(hex.substring(2, 4), 16);
        var b = parseInt(hex.substring(4, 6), 16);
        var a = 255; 

        return [r, g, b, a];
    }

        

    function applyGradientToMissingArea(segmentedImage, originalImage, gradient) {
            
            var originalImageMat = cv.imread(originalImage);
            var segmentedImageMat = cv.imread(segmentedImage);

            var missingPartMask = new cv.Mat();
            cv.cvtColor(segmentedImageMat, missingPartMask, cv.COLOR_RGBA2GRAY, 0);
            cv.threshold(missingPartMask, missingPartMask, 1, 255, cv.THRESH_BINARY_INV);

            cv.resize(gradient, gradient, originalImageMat.size(), 0, 0, cv.INTER_LINEAR);

            var resultMat = new cv.Mat();
            cv.bitwise_and(gradient, gradient, resultMat, missingPartMask);

            cv.addWeighted(resultMat, 1, originalImageMat, 1, 0.0, resultMat);
            

            var resultImage = document.createElement('canvas');
            resultImage.id = 'resultImage';
            resultImage.style.width = '400px';
            cv.imshow(resultImage, resultMat);

            originalImageMat.delete();
            segmentedImageMat.delete();
            missingPartMask.delete();
            resultMat.delete();

            return resultImage;
    }


    document.querySelector('form').addEventListener('submit', function(event) {
        event.preventDefault(); 

        var datasetId = document.querySelector('select[name="dataset"]').value;
        var selectedGradientType = document.querySelector('select[name="gradientType"]').value;

        var formData = new FormData();
        formData.append('dataset', datasetId);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'getDatasetColors.php', true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                var colors = JSON.parse(xhr.responseText);

                var gradient = generateGradient(colors, selectedGradientType);

                var originalImageElement = document.getElementById("originalImage");

                var resultImage = applyGradientToMissingArea(segmentedImage, originalImageElement, gradient);

                displayResultImage(resultImage);
            } else {
                console.error('Errore durante la richiesta dei colori del dataset');
            }
        };
        xhr.send(formData);
    });

    function displayResultImage(resultImage) {
        var previousResult = document.getElementById('resultImage');
        if (previousResult) {
            previousResult.parentNode.removeChild(previousResult);
        }

        var resultContainer = document.createElement('div');
        resultContainer.style.display = 'flex';
        resultContainer.style.justifyContent = 'center';

        resultContainer.appendChild(resultImage);

        document.body.appendChild(resultContainer);
    }

    document.getElementById('saveButton').addEventListener('click', function() {
        var originalImageId = <?php echo $_GET['id']; ?>;
        var resultCanvas = document.getElementById('resultImage');
        var datasetId = document.querySelector('select[name="dataset"]').value;

        var formData = new FormData();
        resultCanvas.toBlob(function(blob) {
        formData.append('finalImage', blob, 'result_image.png'); 
        formData.append('segmentedImageId', selectedImageId); 
        formData.append('originalImageId', originalImageId); 
        formData.append('datasetId', datasetId);

        axios.post('saveFinalImage.php', formData)
            .then(response => {
                console.log(response.data);
            })
            .catch(error => {
                console.error('Errore durante il salvataggio dell\'immagine risultante:', error);
            });
        }, 'image/png');
    });
</script>
<?php
    $conn->close();
?>