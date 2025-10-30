// Firebase Config
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js";
import { getAuth, signInWithEmailAndPassword, signOut } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-auth.js";
import { getFirestore, collection, addDoc, getDocs, deleteDoc, doc } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore.js";

const firebaseConfig = {
  apiKey: "AIzaSyCWA0IS7AmqyIw41iyu_Zf5Y2U9p_G7PHs",
  authDomain: "nipunbet-3d6be.firebaseapp.com",
  projectId: "nipunbet-3d6be",
  storageBucket: "nipunbet-3d6be.firebasestorage.app",
  messagingSenderId: "175096159607",
  appId: "1:175096159607:web:daa57703840233e492d572",
  measurementId: "G-R40HMC5VKK"
};

export const app = initializeApp(firebaseConfig);
export const auth = getAuth(app);
export const db = getFirestore(app);
