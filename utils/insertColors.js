const mysql = require('mysql2/promise'); // Utilizza mysql2 con il supporto delle promesse
const colormaps = require('./colormap.js');

async function insertData() {
  // Configura la connessione al database
  const connection = await mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'tirocinio',
    port: 3307 // Se necessario
  });

  try {
    // Connetti al database
    console.log('Connesso al database MySQL');
    console.log(colormaps);

    for (const [nomeColormap, colors] of Object.entries(colormaps)) {
      // Inserisci il nome della colormap nella tabella Palette
      await connection.execute('INSERT INTO Palette (nome) VALUES (?)', [nomeColormap]);
      console.log('Inserito:', nomeColormap);

      // Ottieni l'id appena inserito della palette
      const [rows] = await connection.execute('SELECT LAST_INSERT_ID() as id');
      const idPalette = rows[0].id;

      // Itera su ogni colore e inseriscilo nella tabella Colore e nella tabella colori_discreti
      for (let i = 0; i < colors.length; i++) {
        const colore = colors[i];
        const [result] = await connection.execute('INSERT INTO Colore (codice) VALUES (?)', [colore]);
        const idColore = result.insertId;

        // Inserisci nella tabella colori_discreti
        await connection.execute('INSERT INTO colori_discreti (id_colore, id_palette, ordine) VALUES (?, ?, ?)', [idColore, idPalette, i]);
        console.log('Inserito colore:', colore);
      }
    }

    console.log('Inserimento completato con successo.');
  } catch (error) {
    console.error('Si è verificato un errore:', error);
  } finally {
    // Chiudi la connessione al database quando hai finito
    await connection.end();
    console.log('Connessione chiusa.');
  }
}

// Chiama la funzione per inserire i dati
insertData();
