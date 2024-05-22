<style>
    .image-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: left;
    }

    .image-box {
        margin: 10px;
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
        position: relative;
    }

    .image-box img {
        max-width: 400px;
        max-height: 300px;
        object-fit: cover;
    }

    .delete-button {
        position: absolute;
        top: 5px;
        right: 5px;
        background-color: #ff0000;
        color: #fff;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
    }
</style>

<div id="app" class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <h2>Filtra in base alla palette</h2>
            <select v-model="selectedPalette" class="form-control">
                <option v-for="(palette, index) in palettes" :value="index">{{ palette.name }}</option>
            </select>
        </div>
    </div>
    <div class="image-container">
        <div v-for="image in images" :key="image.ID" class="image-box">
            <img :src="'finalImages/' + image.nome" alt="Immagine">
            <div class="button-container">
                <button class="delete-button" @click="deleteImage(image.ID, image.nome)">Elimina</button>
                <button class="btn btn-primary save-button" @click="saveImage(image.nome)">Salva</button>
            </div>
        </div>
    </div>
</div>

<script>
    Vue.createApp({
        data() {
            return {
                selectedPalette: null,
                palettes: [],
                images: []
            };
        },
        mounted() {
            this.fetchPalettes(); 
        },
        watch: {
            selectedPalette(newValue, oldValue) {
                this.fetchImages();
            }
        },
        methods: {
            fetchPalettes() {
                axios.get('utils/palettes.php')
                    .then(response => {
                        this.palettes = response.data;
                        if (this.palettes.length > 0) {
                            this.selectedPalette = this.palettes[0].ID; 
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching palettes:', error);
                    });
            },
            fetchImages() {
                axios.get('utils/images.php', {
                    params: {
                        paletteId: this.selectedPalette
                    }
                })
                .then(response => {
                    this.images = response.data;
                })
                .catch(error => {
                    console.error('Error fetching images:', error);
                });
            },
            deleteImage(imageId, imageName) {
                if (confirm("Sei sicuro di voler eliminare questa immagine?")) {
                    axios.post('delete_image.php', {
                        deleteImageId: imageId,
                        deletedImageName: imageName
                    })
                    .then(response => {
                        this.fetchImages(); 
                    })
                    .catch(error => {
                        console.error('Error deleting image:', error);
                    });
                }
            },
            saveImage(imageName) {
                var downloadLink = document.createElement('a');
                downloadLink.href = 'finalImages/' + imageName;
                downloadLink.download = imageName;
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
            }
        }
    }).mount('#app');
</script>
