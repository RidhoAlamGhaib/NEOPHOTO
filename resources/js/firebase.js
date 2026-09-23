import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";

const firebaseConfig = {
  apiKey: "AIzaSy...",
  authDomain: "neophotoid.firebaseapp.com",
  projectId: "neophotoid",
  storageBucket: "neophotoid.firebasestorage.app",
  messagingSenderId: "678773892732",
  appId: "1:678773892732:web:0d10f3bc9cdca5cbe0cbf9",
  measurementId: "G-R0LPVW6BBW"
};

const app = initializeApp(firebaseConfig);

export default app;