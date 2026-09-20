<?php
// Kalkulator PHP sederhana - dapat dijalankan di Termux
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kalkulator PHP</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background:
                radial-gradient(circle at top left, #243b55, transparent 40%),
                radial-gradient(circle at bottom right, #141e30, transparent 45%),
                #080b12;
            color: white;
            padding: 20px;
        }

        .calculator {
            width: 100%;
            max-width: 380px;
            padding: 20px;
            border-radius: 28px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            backdrop-filter: blur(20px);
            box-shadow: 0 25px 70px rgba(0,0,0,0.45);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
        }

        .php-badge {
            font-size: 11px;
            padding: 6px 10px;
            border-radius: 20px;
            background: rgba(255,255,255,0.1);
            color: #aaa;
        }

        .display {
            min-height: 130px;
            padding: 20px 15px;
            margin-bottom: 18px;
            border-radius: 20px;
            background: rgba(0,0,0,0.25);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            text-align: right;
            overflow: hidden;
        }

        .expression {
            color: #8e9aaa;
            font-size: 18px;
            min-height: 27px;
            word-break: break-all;
        }

        .result {
            font-size: 42px;
            font-weight: bold;
            margin-top: 5px;
            word-break: break-all;
        }

        .buttons {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 11px;
        }

        button {
            border: none;
            height: 68px;
            border-radius: 18px;
            background: rgba(255,255,255,0.09);
            color: white;
            font-size: 21px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.15s;
            -webkit-tap-highlight-color: transparent;
        }

        button:active {
            transform: scale(0.92);
        }

        .function {
            color: #ff9f9f;
            background: rgba(255,80,80,0.10);
        }

        .operator {
            color: #8dc8ff;
            background: rgba(50,140,255,0.12);
        }

        .equal {
            background: linear-gradient(135deg, #1677ff, #00a8ff);
            color: white;
        }

        .zero {
            grid-column: span 2;
        }

        .history {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .history-title {
            font-size: 13px;
            color: #8e9aaa;
            margin-bottom: 10px;
        }

        #historyList {
            max-height: 100px;
            overflow-y: auto;
            font-size: 13px;
            color: #cbd3df;
        }

        .history-item {
            display: flex;
            justify-content: space-between;
            padding: 7px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        @media (max-width: 400px) {
            .calculator {
                padding: 15px;
            }

            button {
                height: 62px;
            }

            .result {
                font-size: 36px;
            }
        }
    </style>
</head>

<body>

<div class="calculator">

    <div class="header">
        <div class="title">Kalkulator</div>
        <div class="php-badge">By NoOp_PrO</div>
    </div>

    <div class="display">
        <div class="expression" id="expression"></div>
        <div class="result" id="result">0</div>
    </div>

    <div class="buttons">

        <button class="function" onclick="clearDisplay()">AC</button>
        <button class="function" onclick="deleteLast()">DEL</button>
        <button class="function" onclick="percentage()">%</button>
        <button class="operator" onclick="appendOperator('/')">÷</button>

        <button onclick="appendNumber('7')">7</button>
        <button onclick="appendNumber('8')">8</button>
        <button onclick="appendNumber('9')">9</button>
        <button class="operator" onclick="appendOperator('*')">×</button>

        <button onclick="appendNumber('4')">4</button>
        <button onclick="appendNumber('5')">5</button>
        <button onclick="appendNumber('6')">6</button>
        <button class="operator" onclick="appendOperator('-')">−</button>

        <button onclick="appendNumber('1')">1</button>
        <button onclick="appendNumber('2')">2</button>
        <button onclick="appendNumber('3')">3</button>
        <button class="operator" onclick="appendOperator('+')">+</button>

        <button class="zero" onclick="appendNumber('0')">0</button>
        <button onclick="appendDecimal()">.</button>
        <button class="equal" onclick="calculate()">=</button>

    </div>

    <div class="history">
        <div class="history-title">RIWAYAT PERHITUNGAN</div>
        <div id="historyList"></div>
    </div>

</div>

<script>

let currentInput = "";
let expressionText = "";
let justCalculated = false;

const resultDisplay = document.getElementById("result");
const expressionDisplay = document.getElementById("expression");
const historyList = document.getElementById("historyList");

function appendNumber(number) {

    if (justCalculated) {
        currentInput = "";
        expressionText = "";
        justCalculated = false;
    }

    currentInput += number;
    expressionText += number;

    updateDisplay();
}

function appendDecimal() {

    if (justCalculated) {
        currentInput = "";
        expressionText = "";
        justCalculated = false;
    }

    const parts = currentInput.split(/[\+\-\*\/]/);
    const lastNumber = parts[parts.length - 1];

    if (!lastNumber.includes(".")) {

        if (lastNumber === "") {
            currentInput += "0";
            expressionText += "0";
        }

        currentInput += ".";
        expressionText += ".";
    }

    updateDisplay();
}

function appendOperator(operator) {

    if (currentInput === "" && expressionText === "") {
        return;
    }

    if (/[+\-*\/]$/.test(currentInput)) {
        currentInput = currentInput.slice(0, -1);
        expressionText = expressionText.slice(0, -1);
    }

    currentInput += operator;
    expressionText += operator;

    justCalculated = false;

    updateDisplay();
}

function clearDisplay() {

    currentInput = "";
    expressionText = "";
    resultDisplay.textContent = "0";
    expressionDisplay.textContent = "";
    justCalculated = false;
}

function deleteLast() {

    currentInput = currentInput.slice(0, -1);
    expressionText = expressionText.slice(0, -1);

    updateDisplay();
}

function percentage() {

    const match = currentInput.match(/(\d+\.?\d*)$/);

    if (!match) return;

    const number = parseFloat(match[0]);
    const percent = number / 100;

    currentInput =
        currentInput.slice(0, -match[0].length) +
        percent;

    expressionText =
        expressionText.slice(0, -match[0].length) +
        percent;

    updateDisplay();
}

function calculate() {

    if (!currentInput) return;

    try {

        if (/[+\-*\/.]$/.test(currentInput)) {
            return;
        }

        // Evaluasi ekspresi kalkulator
        const result = Function(
            '"use strict"; return (' + currentInput + ')'
        )();

        if (!Number.isFinite(result)) {
            throw new Error();
        }

        const formattedResult =
            Number.isInteger(result)
            ? result
            : parseFloat(result.toFixed(10));

        expressionDisplay.textContent =
            formatExpression(currentInput) + " =";

        resultDisplay.textContent = formattedResult;

        addHistory(
            formatExpression(currentInput),
            formattedResult
        );

        currentInput = String(formattedResult);
        expressionText = String(formattedResult);

        justCalculated = true;

    } catch (error) {

        resultDisplay.textContent = "Error";
    }
}

function formatExpression(expression) {

    return expression
        .replace(/\*/g, "×")
        .replace(/\//g, "÷")
        .replace(/-/g, "−");
}

function updateDisplay() {

    expressionDisplay.textContent =
        formatExpression(expressionText);

    if (currentInput !== "") {
        resultDisplay.textContent = currentInput
            .replace(/\*/g, "×")
            .replace(/\//g, "÷")
            .replace(/-/g, "−");
    } else {
        resultDisplay.textContent = "0";
    }
}

function addHistory(expression, result) {

    const item = document.createElement("div");

    item.className = "history-item";

    item.innerHTML =
        `<span>${expression}</span>
         <strong>${result}</strong>`;

    historyList.prepend(item);

    while (historyList.children.length > 10) {
        historyList.removeChild(historyList.lastChild);
    }
}

// Keyboard support
document.addEventListener("keydown", function(event) {

    const key = event.key;

    if (key >= "0" && key <= "9") {
        appendNumber(key);
    }

    else if (key === ".") {
        appendDecimal();
    }

    else if (["+", "-", "*", "/"].includes(key)) {
        appendOperator(key);
    }

    else if (key === "Enter" || key === "=") {
        calculate();
    }

    else if (key === "Backspace") {
        deleteLast();
    }

    else if (key === "Escape") {
        clearDisplay();
    }

    else if (key === "%") {
        percentage();
    }

});

</script>

</body>
</html>
