// Importation des fonctions Firebase
import { initializeApp } from "firebase/app";
import { getStorage } from "firebase/storage";

// Configuration Firebase (remplace par tes propres infos)
const firebaseConfig = {
  apiKey: "AIzaSyDpMlQ-sNAS9FwZ2KiDMvu8mf_6lI0dfMY",
  authDomain: "projet-portfolio-816fb.firebaseapp.com",
  projectId: "projet-portfolio-816fb",
  storageBucket: "projet-portfolio-816fb.appspot.com", // Correction ici
  messagingSenderId: "324731250383",
  appId: "1:324731250383:web:2bcefddfd695b98e18f9ba",
  measurementId: "G-0DTVGX3DYZ"
};

// Initialisation de Firebase
const app = initializeApp(firebaseConfig);
const storage = getStorage(app); // Initialisation de Firebase Storage

export { storage };
