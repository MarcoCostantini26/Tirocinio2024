<div id="app" class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <div v-if="datasets.length > 0">
                <h2>Lista dei Dataset</h2>
                <ul class="list-group">
                    <li v-for="dataset in datasets" :key="dataset.ID" class="list-group-item d-flex justify-content-between align-items-center">
                        {{ dataset.nome }}
                        <div>
                            <button @click="editDataset(dataset)" class="btn btn-primary btn-sm me-2">Modifica</button>
                            <button @click="deleteDataset(dataset)" class="btn btn-danger btn-sm">Elimina</button>
                        </div>
                    </li>
                </ul>
            </div>
            <div v-else>
                <p>Nessun dataset presente.</p>
            </div>
        </div>
        <div class="col-md-6">
            <h2>Aggiungi Dataset</h2>
            <button class="btn btn-primary mb-3" @click="showForm = true">Aggiungi nuovo dataset</button>
                <form v-if="showForm" @submit.prevent="saveDataset">
                    <div class="mb-3">
                        <label for="datasetName" class="form-label">Nome del Dataset</label>
                        <input type="text" class="form-control" id="datasetName" v-model="newDataset.name" required>
                    </div>
                    <div v-for="(dataset, index) in newDataset.datasets" :key="index">
                        <h4>Dataset {{ index + 1 }}</h4>
                        <div class="mb-3">
                            <label for="dsName" class="form-label">Nome del Dataset</label>
                            <input type="text" class="form-control" id="dsName" v-model="dataset.ds_name" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="minValue" class="form-label">Valore Minimo</label>
                                <input type="number" class="form-control" id="minValue" v-model="dataset.min_value" required>
                            </div>
                            <div class="col-md-6">
                                <label for="maxValue" class="form-label">Valore Massimo</label>
                                <input type="number" class="form-control" id="maxValue" v-model="dataset.max_value" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <h5>Dati</h5>
                            <div v-for="(dataItem, dataIndex) in dataset.data" :key="dataIndex">
                                <label for="dataDescription" class="form-label">Descrizione</label>
                                <input type="text" class="form-control" v-model="dataItem.description" required>
                                <label for="dataValue" class="form-label">Valore</label>
                                <input type="number" class="form-control" v-model="dataItem.value" required>
                            </div>
                            <button type="button" class="btn btn-secondary" @click="addData(dataset)">Aggiungi Dato</button>
                            <button type="button" class="btn btn-danger" @click="removeDataset(index)">Rimuovi Dataset</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" @click="addDataset">Aggiungi Dataset</button>
                    <button type="submit" class="btn btn-success">Salva Dataset</button>
                </form>
        </div>
    </div>
</div>

<script>
    Vue.createApp({
        data() {
            return {
                datasets: [],
                newDataset: {
                    name: "",
                    datasets: [{
                        ds_name: "",
                        min_value: null,
                        max_value: null,
                        data: [{
                            description: "",
                            value: null
                        }]
                    }]
                },
                showForm: false
            };
        },
        mounted() {
            this.fetchDatasets();
        },
        methods: {
            fetchDatasets() {
                axios.get('utils/getDataset.php')
                    .then(response => {
                        this.datasets = response.data;
                    })
                    .catch(error => {
                        console.error('Errore durante il recupero dei dataset:', error);
                    });
            },
            addDataset() {
                this.newDataset.datasets.push({
                    ds_name: "",
                    min_value: null,
                    max_value: null,
                    data: [{
                        description: "",
                        value: null
                    }]
                });
            },
            editDataset(dataset) {
                axios.get(`utils/getDatasetInfo.php?id=${dataset.ID}`)
                .then(response => {
                    const responseData = response.data;
                    this.newDataset = {
                        ID: responseData.ID,
                        name: responseData.name,
                        datasets: responseData.datasets.map(dataset => ({
                            ds_name: dataset.ds_name,
                            min_value: dataset.min_value,
                            max_value: dataset.max_value,
                            data: dataset.data ? [{
                                data_id: dataset.data.data_id,
                                description: dataset.data.description,
                                value: dataset.data.value
                            }] : []
                        }))
                    };
                    this.showForm = true;
                })
                .catch(error => {
                    console.error('Errore durante il recupero dei dettagli del dataset:', error);
                });
            },
            deleteDataset(dataset) {
                axios.delete(`utils/deleteDataset.php?id=${dataset.ID}`)
                    .then(response => {
                        console.log(response.data);
                        const index = this.datasets.indexOf(dataset);
                        if (index !== -1) {
                            this.datasets.splice(index, 1);
                        }
                    })
                    .catch(error => {
                        console.error('Errore durante l\'eliminazione del dataset:', error);
                    });
            },
            addData(dataset) {
                dataset.data.push({
                    description: "",
                    value: null
                });
            },
            removeDataset(index) {
                this.newDataset.datasets.splice(index, 1);
            },
            saveDataset() {
                const endpoint = 'utils/updateDataset.php';

                axios.post(endpoint, this.newDataset)
                    .then(response => {
                        console.log(response.data);
                        this.newDataset = {
                            name: "",
                            datasets: [{
                                ds_name: "",
                                min_value: null,
                                max_value: null,
                                data: [{
                                    description: "",
                                    value: null
                                }]
                            }]
                        };
                        this.fetchDatasets();
                        this.showForm = false;
                    })
                    .catch(error => {
                        console.error('Errore durante il salvataggio del dataset:', error);
                    });
            }
        }
    }).mount('#app');
</script>
