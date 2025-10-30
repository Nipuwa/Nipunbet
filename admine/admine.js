import { auth, db } from "./firebase.js";
import { signOut } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-auth.js";
import { collection, addDoc, getDocs, deleteDoc, doc } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore.js";

const logoutBtn = document.getElementById("logoutBtn");
logoutBtn.addEventListener("click", async () => {
  await signOut(auth);
  window.location.href = "index.html";
});

const addSignalBtn = document.getElementById("addSignalBtn");
const signalList = document.getElementById("signalList");

addSignalBtn.addEventListener("click", async () => {
  const title = document.getElementById("signalTitle").value;
  const details = document.getElementById("signalDetails").value;

  if (title && details) {
    await addDoc(collection(db, "signals"), {
      title: title,
      details: details,
      createdAt: new Date()
    });
    alert("Signal added!");
    location.reload();
  }
});

async function loadSignals() {
  const querySnapshot = await getDocs(collection(db, "signals"));
  querySnapshot.forEach((docSnap) => {
    const data = docSnap.data();
    const div = document.createElement("div");
    div.className = "signal-card";
    div.innerHTML = `
      <h4>${data.title}</h4>
      <p>${data.details}</p>
      <button onclick="deleteSignal('${docSnap.id}')">Delete</button>
    `;
    signalList.appendChild(div);
  });
}

window.deleteSignal = async (id) => {
  await deleteDoc(doc(db, "signals", id));
  alert("Deleted!");
  location.reload();
};

loadSignals();
