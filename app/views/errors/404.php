<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 404 - Chess Game</title>
    <style>
        /* Reset styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #ff7eb3, #ff758c, #fdc8c4);
            background-size: 400% 400%;
            animation: gradientBackground 15s ease infinite;
            color: #fff;
        }

        @keyframes gradientBackground {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            text-align: center;
            max-width: 500px;
            margin-bottom: 2rem;
        }

        .container h1 {
            font-size: 5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
        }

        .container p {
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }

        .container .button {
            display: inline-block;
            padding: 0.8rem 2rem;
            font-size: 1.2rem;
            font-weight: bold;
            color: #ff758c;
            background: #fff;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .container .button:hover {
            background: #ffe4e1;
            transform: scale(1.1);
        }

        #board {
            display: grid;
            grid-template-columns: repeat(8, 40px);
            grid-template-rows: repeat(8, 40px);
            gap: 0;
            border: 2px solid #000;
            margin-top: 2rem;
        }

        .square {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .white {
            background-color: #f0d9b5;
        }

        .black {
            background-color: #b58863;
        }

        .info {
            margin-top: 1rem;
            font-size: 1.2rem;
            color: #000;
        }

        .highlight {
            background-color: yellow !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Error 404</h1>
        <p style="color: black;">Oops! Esta página no existe.</p>
        <a href="index.php?vista=home" class="button">Volver al inicio</a>
    </div>

    <!--div id="board"></div>
    <div class="info" id="gameInfo"></div-->

    <script>
        const boardElement = document.getElementById('board');
        const gameInfo = document.getElementById('gameInfo');

        // Initialize the chessboard state
        const initialBoard = [
            ['r', 'n', 'b', 'q', 'k', 'b', 'n', 'r'],
            ['p', 'p', 'p', 'p', 'p', 'p', 'p', 'p'],
            [null, null, null, null, null, null, null, null],
            [null, null, null, null, null, null, null, null],
            [null, null, null, null, null, null, null, null],
            [null, null, null, null, null, null, null, null],
            ['P', 'P', 'P', 'P', 'P', 'P', 'P', 'P'],
            ['R', 'N', 'B', 'Q', 'K', 'B', 'N', 'R']
        ];

        const pieceSymbols = {
            'P': '♙', 'R': '♖', 'N': '♘', 'B': '♗', 'Q': '♕', 'K': '♔',
            'p': '♟', 'r': '♜', 'n': '♞', 'b': '♝', 'q': '♛', 'k': '♚'
        };

        let selectedSquare = null;
        let currentPlayer = 'white';
        let moveCount = 0;

        function renderBoard() {
            boardElement.innerHTML = '';
            initialBoard.forEach((row, rowIndex) => {
                row.forEach((cell, colIndex) => {
                    const square = document.createElement('div');
                    square.classList.add('square');
                    square.classList.add((rowIndex + colIndex) % 2 === 0 ? 'white' : 'black');
                    square.dataset.row = rowIndex;
                    square.dataset.col = colIndex;
                    if (cell) {
                        square.textContent = pieceSymbols[cell];
                        square.dataset.piece = cell;
                    }
                    square.addEventListener('click', onSquareClick);
                    boardElement.appendChild(square);
                });
            });
        }

        function onSquareClick(event) {
            const square = event.target;
            const row = parseInt(square.dataset.row, 10);
            const col = parseInt(square.dataset.col, 10);

            if (selectedSquare) {
                const fromRow = parseInt(selectedSquare.dataset.row, 10);
                const fromCol = parseInt(selectedSquare.dataset.col, 10);

                if (isValidMove(fromRow, fromCol, row, col)) {
                    makeMove(fromRow, fromCol, row, col);
                    moveCount++;
                    currentPlayer = currentPlayer === 'white' ? 'black' : 'white';
                }
                selectedSquare.classList.remove('highlight');
                selectedSquare = null;
            } else if (square.dataset.piece) {
                const pieceColor = square.dataset.piece === square.dataset.piece.toUpperCase() ? 'white' : 'black';
                if (pieceColor === currentPlayer) {
                    selectedSquare = square;
                    square.classList.add('highlight');
                }
            }

            renderBoard();
            updateGameInfo();
        }

        function isValidMove(fromRow, fromCol, toRow, toCol) {
            // Simplified move validation: just check that the destination is empty or contains an opponent piece
            const piece = initialBoard[fromRow][fromCol];
            const target = initialBoard[toRow][toCol];
            if (!piece) return false;

            const isOpponent = target && (target === target.toUpperCase() !== (piece === piece.toUpperCase()));
            return !target || isOpponent;
        }

        function makeMove(fromRow, fromCol, toRow, toCol) {
            initialBoard[toRow][toCol] = initialBoard[fromRow][fromCol];
            initialBoard[fromRow][fromCol] = null;
        }

        function updateGameInfo() {
            gameInfo.textContent = `Current player: ${currentPlayer} | Moves: ${moveCount}`;
        }

        renderBoard();
        updateGameInfo();
    </script>
</body>
</html>
