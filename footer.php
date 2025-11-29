<footer>
    <div class="footer-container">
        <div class="footer-section">
            <h4>Síguenos</h4>
            <div class="social-icons">
                <!-- Facebook -->
                <a href="https://www.facebook.com/share/1G5mmp6dPx/?mibextid=wwXIfr" target="_blank" class="social-link facebook">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#fff">
                        <path d="M22.675 0h-21.35C.597 0 0 .593 0 1.326v21.348C0 23.407.597 24 1.325 24H12.82V14.708h-3.41v-3.62h3.41V8.413c0-3.378 2.065-5.22 5.084-5.22 1.443 0 2.682.107 3.043.155v3.525l-2.083.001c-1.632 0-1.948.776-1.948 1.914v2.512h3.893l-.507 3.62h-3.386V24h6.644c.728 0 1.324-.593 1.324-1.326V1.326C24 .593 23.404 0 22.675 0z"/>
                    </svg>
                </a>

                <!-- WhatsApp -->
                <a href="https://wa.me/526951105779" target="_blank" class="social-link whatsapp">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="#fff">
                        <path d="M19.11 17.4c-.29-.15-1.7-.84-1.97-.93-.26-.1-.45-.15-.64.15-.19.29-.74.93-.91 1.12-.17.19-.34.22-.63.07-.29-.15-1.22-.45-2.33-1.45-.86-.77-1.44-1.72-1.61-2-.17-.29-.02-.44.13-.58.14-.14.29-.34.44-.5.15-.17.19-.29.29-.48.1-.19.05-.36-.02-.51-.07-.15-.64-1.55-.88-2.13-.23-.56-.47-.48-.64-.49h-.55c-.19 0-.5.07-.77.36-.26.29-1 1-1 2.43s1.03 2.82 1.18 3.01c.15.19 2.03 3.1 4.91 4.35.69.3 1.22.48 1.64.62.69.22 1.32.19 1.82.12.56-.08 1.7-.69 1.94-1.36.24-.67.24-1.25.17-1.36-.07-.12-.26-.19-.55-.34zM16 3C8.83 3 3 8.83 3 16c0 2.83.88 5.45 2.37 7.63L3 29l5.52-2.34A12.94 12.94 0 0 0 16 29c7.17 0 13-5.83 13-13S23.17 3 16 3zm0 23.75c-2.33 0-4.49-.69-6.31-1.88l-.45-.29-3.27 1.38.7-3.45-.22-.36A10.75 10.75 0 1 1 16 26.75z"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="footer-section">
            <h4>Contacto</h4>
            <p>Correo: vinculacion@utescuinapa.edu.mx</p>
            <p>Teléfono: +52 695 110 5779 </p>
        </div>

        <div class="footer-section">
            <h4>Derechos</h4>
            <p>© 2025 UTESC. Todos los derechos reservados. Juan Osuna, Tonantzin Morales.</p>
        </div>
    </div>
</footer>

<style>
footer {
    background-color: #1b2c3b;
    color: #fff;
    padding: 20px 10%;
}

.footer-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
}

.footer-section {
    flex: 1;
    min-width: 200px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.footer-section h4 {
    margin-bottom: 10px;
    font-size: 18px;
}

.footer-section p {
    margin: 5px 0;
    line-height: 1.5;
    font-size: 16px;
}

/* Íconos sociales */
.social-icons {
    display: flex;
    gap: 15px;
}

.footer-section a.social-link {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
}

/* Colores específicos */
.social-link.facebook {
    background-color: #3b5998;
}
.social-link.whatsapp {
    background-color: #25D366;
}

.footer-section a.social-link svg {
    width: 24px;
    height: 24px;
    transition: transform 0.3s ease;
}

.footer-section a.social-link:hover {
    transform: scale(1.2) rotate(5deg);
    box-shadow: 0 0 15px rgba(255,255,255,0.4);
}

@media (max-width: 768px) {
    .footer-container {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .footer-section {
        align-items: center;
        margin-bottom: 15px;
    }
    .social-icons {
        justify-content: center;
    }
}
</style>
