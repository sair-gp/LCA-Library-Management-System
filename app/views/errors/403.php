<style>
    /* Reset styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .error403 {
        font-family: 'Arial', sans-serif;
        height: 100vh;
        overflow: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
        background: linear-gradient(135deg, #888888, #b0b0b0, #a0a0a0);
        background-size: 400% 400%;
        animation: gradientBackground 10s ease infinite;
        color: #333;
        flex-direction: column;
    }

    @keyframes gradientBackground {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .container {
        text-align: center;
        max-width: 500px;
        margin-bottom: 20px;
    }

    .container h1 {
        font-size: 4rem;
        margin-bottom: 1rem;
        color: #222;
        text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
    }

    .container p {
        font-size: 1.5rem;
        margin-bottom: 2rem;
        color: #444;
    }

    .container .button {
        display: inline-block;
        padding: 0.8rem 2rem;
        font-size: 1.2rem;
        font-weight: bold;
        color: #333;
        background: #e0e0e0;
        border: none;
        border-radius: 50px;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .container .button:hover {
        background: #cfcfcf;
        transform: scale(1.1);
    }

    /* Canvas for Dino Game */
    #dino-game {
        width: 80%;
        max-width: 600px;
        height: 200px;
        background: #f0f0f0;
        border: 2px solid #ccc;
        position: relative;
        overflow: hidden;
    }

    /* Dino */
    .dino {
        width: 25px; /* Ajustamos el tamaño */
        height: 35px;
        background-image: url('https://www.muycomputer.com/wp-content/uploads/2020/08/dinoswords.gif'); /* Dino image */
        background-size: contain;
        background-position: center;
        position: absolute;
        bottom: 20px;
        left: 20px;
    }

    /* Cactus */
    .cactus {
        width: 15px; /* Reducimos el ancho del cactus */
        height: 40px;
        background: #555;
        position: absolute;
        bottom: 20px;
        right: -20px;
        animation: moveCactus 3s linear infinite;
    }

    @keyframes moveCactus {
        from {
            right: -20px;
        }
        to {
            right: 100%;
        }
    }

    /* Game Over Message */
    .game-over {
        font-size: 1.5rem;
        color: red;
        text-align: center;
        margin-top: 20px;
    }

    /* Score Display */
    .score {
        font-size: 1.5rem;
        color: #333;
        position: absolute;
        top: 10px;
        left: 10px;
    }
</style>

<div class="error403">
    <div class="container">
        <h1>Error 403</h1>
        <p>"Oops! No tienes permiso para acceder a esta página."</p>
        <a href="index.php?vista=home" class="button">Volver al inicio</a>
    </div>

    <!-- Dino Game Canvas -->
    <!--div id="dino-game">
        <div class="dino" id="dino"></div>
        <div class="score" id="score">Puntaje: 0</div>
    </div>
    <p class="game-over" id="game-over" style="display: none;">¡Juego terminado! Presiona R para reiniciar.</p-->
</div>

<script>
    // Dino Game Variables
    const dino = document.getElementById("dino");
    const gameArea = document.getElementById("dino-game");
    const gameOverText = document.getElementById("game-over");
    const scoreDisplay = document.getElementById("score");
    let isJumping = false;
    let cactusInterval;
    let isGameOver = false;
    let score = 0;

    // Dino Jump Logic
    function jump() {
        if (isJumping) return;

        let position = 20;  // Starting at the same line
        isJumping = true;

        const upInterval = setInterval(() => {
            if (position >= 100) {
                clearInterval(upInterval);
                const downInterval = setInterval(() => {
                    if (position <= 20) {
                        clearInterval(downInterval);
                        isJumping = false;
                    }
                    position -= 5;
                    dino.style.bottom = position + "px";
                }, 20);
            }
            position += 5;
            dino.style.bottom = position + "px";
        }, 20);
    }

    // Spawn Cactus
    function spawnCactus() {
        const cactus = document.createElement("div");
        cactus.classList.add("cactus");
        gameArea.appendChild(cactus);

        cactus.addEventListener("animationend", () => {
            cactus.remove();
            if (!isGameOver) {
                score++;
                scoreDisplay.textContent = `Puntaje: ${score}`;
            }
        });

        // Collision Detection
        const collisionCheck = setInterval(() => {
            const dinoRect = dino.getBoundingClientRect();
            const cactusRect = cactus.getBoundingClientRect();

            if (
                dinoRect.left < cactusRect.right &&
                dinoRect.right > cactusRect.left &&
                dinoRect.bottom > cactusRect.top &&
                dinoRect.top < cactusRect.bottom
            ) {
                clearInterval(collisionCheck);
                gameOver();
            }
        }, 50);
    }

    // Game Over Logic
    function gameOver() {
        isGameOver = true;
        clearInterval(cactusInterval);
        gameOverText.style.display = "block";
        document.querySelectorAll(".cactus").forEach(cactus => cactus.remove());
    }

    // Restart Game
    function restartGame() {
        if (!isGameOver) return;

        isGameOver = false;
        score = 0;
        scoreDisplay.textContent = `Puntaje: ${score}`;
        gameOverText.style.display = "none";
        cactusInterval = setInterval(spawnCactus, 1500);
    }

    // Event Listeners
    document.addEventListener("keydown", (e) => {
        if (e.code === "Space") jump();
        if (e.code === "KeyR") restartGame();
    });

    // Start Game
    cactusInterval = setInterval(spawnCactus, 1500);
</script>
