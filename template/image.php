<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tirocinio";

$conn = new mysqli($servername, $username, $password, $dbname, 3307);

if ($conn->connect_error) {
    die("Connessione al database fallita: " . $conn->connect_error);
}

$sql_images = "SELECT ID, nome FROM images";
$result_images = $conn->query($sql_images);

$sql_segmented_images = "SELECT ID, ID_ImmagineOriginale, nome FROM segmented_images";
$result_segmented_images = $conn->query($sql_segmented_images);
?>

<div class="container-fluid h-100">
    <div class="row h-100">
    <div class="col-md-6 p-5">
            <a href="generation.php" class="d-block text-decoration-none">
                <div class="bg-primary p-5 text-center text-white h-100 opacity-hover">
                    <h2>Carica Immagine</h2>
                    <p>Carica una nuova immagine per iniziare</p>
                </div>
            </a>
            <h2 class="mt-4">Immagini Originali</h2>
            <?php
            if ($result_images->num_rows > 0) {
                while ($row = $result_images->fetch_assoc()) {
                    echo "<div class='mb-3'>";
                    echo "<img src='" . $row['nome'] . "' alt='Immagine' style='max-width: 100px;' class='original-image' data-id='" . $row['ID'] . "'>";
                    echo "<button class='btn btn-danger delete-image-btn' data-id='" . $row['ID'] . "'>Elimina</button>";
                    echo "</div>";
                }
            } else {
                echo "Nessuna immagine trovata.";
            }
            ?>
        </div>
        <div class="col-md-6 p-5">
            <a href="generate_image.php" class="d-block text-decoration-none">
                <div class="bg-success p-5 text-center text-white h-100 opacity-hover">
                    <h2>Genera Immagine</h2>
                    <p>Genera un'immagine basata sui dati inseriti</p>
                </div>
            </a>
            <h2 class="mt-4">Immagini Segmentate</h2>
            <?php
            if ($result_segmented_images->num_rows > 0) {
                echo "<select id='segmented_image_select' class='form-select'>";
                while ($row = $result_segmented_images->fetch_assoc()) {
                    echo "<option value='" . $row['nome'] . "' data-original-id='" . $row['ID_ImmagineOriginale'] . "'>" . $row['nome'] . "</option>";
                }
                echo "</select>";
            } else {
                echo "Nessuna immagine segmentata trovata.";
            }
            ?>
            <div id="segmented_image_display" class="mt-3">
                <!-- Qui verrà visualizzata l'immagine segmentata selezionata -->
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var select = document.getElementById('segmented_image_select');
        var segmentedImageDisplay = document.getElementById('segmented_image_display');
        
        select.addEventListener('change', function() {
            var selectedImage = this.value;
            segmentedImageDisplay.innerHTML = "<img src='" + selectedImage + "' alt='Immagine Segmentata' style='max-width: 100%;'>";
        });
        
        var originalImages = document.querySelectorAll('.original-image');
        originalImages.forEach(function(image) {
            image.addEventListener('click', function() {
                var originalId = this.getAttribute('data-id');
                Array.from(select.options).forEach(function(option) {
                    if (option.getAttribute('data-original-id') === originalId) {
                        option.style.display = 'block';
                    } else {
                        option.style.display = 'none';
                    }
                });
                segmentedImageDisplay.innerHTML = ""; 
                var selectedImageSrc = this.getAttribute('src');
                segmentedImageDisplay.innerHTML = "<img src='" + selectedImageSrc + "' alt='Immagine Originale' style='max-width: 80%;'>";
            });
        });
        var deleteButtons = document.querySelectorAll('.delete-image-btn');
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var imageId = this.getAttribute('data-id');
                fetch('delete_image.php?id=' + imageId)
                .then(response => {
                    if (response.ok) {
                        alert("Immagine eliminata con successo!");
                        window.location.reload();
                    } else {
                        alert("Si è verificato un errore durante l'eliminazione dell'immagine.");
                    }
                })
                .catch(error => {
                    console.error('Si è verificato un errore:', error);
                });
            });
        });
    });
</script>

<?php
$conn->close();
?>
