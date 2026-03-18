<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>CE&P Corporation – Lucky Draw</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Exo+2:wght@300;400;600;700;900&display=swap"
        rel="stylesheet" />
    <style>
        :root {
            --blue-dark: #0d2b6b;
            --blue-mid: #1a4bbf;
            --blue-light: #4a9ded;
            --sky: #c6e4f8;
            --red: #e02020;
            --gold: #f9c74f;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Exo 2', sans-serif;
            background: linear-gradient(135deg, #b8d8f5 0%, #e8f4ff 40%, #cce8ff 70%, #a8d0f0 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* animated background blobs */
        body::before,
        body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: .4;
            pointer-events: none;
            z-index: 0;
        }

        body::before {
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, #5ab3f7 0%, transparent 70%);
            top: -100px;
            left: -150px;
            animation: blobA 12s ease-in-out infinite alternate;
        }

        body::after {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, #a0d4ff 0%, transparent 70%);
            bottom: -80px;
            right: -100px;
            animation: blobB 14s ease-in-out infinite alternate;
        }

        @keyframes blobA {
            to {
                transform: translate(80px, 60px) scale(1.1);
            }
        }

        @keyframes blobB {
            to {
                transform: translate(-60px, -80px) scale(1.15);
            }
        }

        /* floating diamonds */
        .diamond {
            position: fixed;
            width: 38px;
            height: 38px;
            background: rgba(255, 255, 255, .16);
            transform: rotate(45deg);
            border: 1px solid rgba(255, 255, 255, .4);
            border-radius: 4px;
            pointer-events: none;
            z-index: 0;
            animation: floatD 8s ease-in-out infinite alternate;
        }

        .d1 {
            top: 8%;
            left: 5%;
        }

        .d2 {
            top: 22%;
            right: 6%;
            width: 24px;
            height: 24px;
            animation-delay: 2s;
        }

        .d3 {
            bottom: 14%;
            left: 8%;
            width: 28px;
            height: 28px;
            animation-delay: 4s;
        }

        .d4 {
            top: 55%;
            right: 3%;
            width: 18px;
            height: 18px;
            animation-delay: 1s;
        }

        @keyframes floatD {
            from {
                transform: rotate(45deg) translateY(0);
            }

            to {
                transform: rotate(45deg) translateY(-22px);
            }
        }

        /* HEADER */
        header {
            position: relative;
            z-index: 10;
            background: linear-gradient(90deg, var(--blue-dark) 0%, var(--blue-mid) 60%, var(--blue-light) 100%);
            padding: 14px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-shadow: 0 4px 30px rgba(13, 43, 107, .5);
        }

        header img.logo {
            height: 60px;
            filter: drop-shadow(0 0 12px rgba(255, 255, 255, 0.8)) drop-shadow(0 2px 8px rgba(0, 0, 0, .4));
        }

        .fallback-logo {
            color: white;
            font-size: 1.9rem;
            font-weight: 900;
            letter-spacing: .04em;
            line-height: 1;
        }

        .fallback-logo span.amp {
            color: var(--gold);
        }

        .fallback-logo small {
            font-size: .7rem;
            font-weight: 300;
            display: block;
            letter-spacing: .15em;
        }

        .header-center {
            text-align: center;
            color: white;
            flex: 1;
        }

        .header-center h1 {
            font-family: 'Battambang', serif;
            font-size: 1.45rem;
            font-weight: 700;
            text-shadow: 0 2px 12px rgba(0, 0, 0, .4);
        }

        .header-center p {
            font-size: .78rem;
            opacity: .75;
            letter-spacing: .06em;
        }

        .header-badge {
            background: var(--red);
            color: white;
            border-radius: 12px;
            padding: 8px 18px;
            font-weight: 700;
            font-size: .82rem;
            letter-spacing: .05em;
            box-shadow: 0 3px 14px rgba(224, 32, 32, .55);
            white-space: nowrap;
        }

        /* MAIN LAYOUT */
        .main-wrap {
            position: relative;
            z-index: 5;
            max-width: 1180px;
            margin: 36px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 28px;
            align-items: start;
        }

        /* GLASS CARD */
        .gc {
            background: rgba(255, 255, 255, .60);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1.5px solid rgba(255, 255, 255, .85);
            border-radius: 24px;
            box-shadow: 0 8px 40px rgba(13, 43, 107, .16);
            padding: 28px 30px;
        }

        /* SECTION TITLE */
        .stitle {
            font-family: 'Battambang', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--blue-dark);
            margin-bottom: 20px;
        }

        /* SLOTS */
        .slots {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 11px;
            margin-bottom: 24px;
        }

        .slot {
            background: rgba(255, 255, 255, .55);
            border: 1.5px solid rgba(255, 255, 255, .85);
            border-radius: 13px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 50px;
            transition: all .3s;
            box-shadow: 0 2px 8px rgba(74, 157, 237, .1);
        }

        .slot.won {
            background: linear-gradient(135deg, #fffce0, #ffe87a);
            border-color: var(--gold);
            box-shadow: 0 4px 20px rgba(249, 199, 79, .45);
            animation: wPulse .5s ease;
        }

        @keyframes wPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.04);
            }
        }

        .snum {
            width: 26px;
            height: 26px;
            background: var(--blue-mid);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .72rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .slot.won .snum {
            background: var(--red);
        }

        .sname {
            font-family: 'Battambang', serif;
            font-size: .88rem;
            color: var(--blue-dark);
        }

        /* INPUT ROW */
        .irow {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: center;
        }

        .irow input {
            flex: 1;
            border: 2px solid rgba(26, 75, 191, .25);
            border-radius: 50px;
            padding: 12px 20px;
            font-size: .9rem;
            font-family: 'Battambang', serif;
            background: rgba(255, 255, 255, .8);
            outline: none;
            transition: border-color .2s;
        }

        .irow input:focus {
            border-color: var(--blue-mid);
        }

        .btn-draw {
            background: linear-gradient(135deg, var(--blue-dark), var(--blue-mid));
            color: white;
            border: none;
            border-radius: 50px;
            padding: 12px 26px;
            font-family: 'Battambang', serif;
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(13, 43, 107, .4);
            transition: all .2s;
            white-space: nowrap;
        }

        .btn-draw:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 22px rgba(13, 43, 107, .5);
        }

        /* PRIZE PANEL */
        .prize-panel {
            text-align: center;
            position: static;
            /* remove sticky behavior to avoid sticking on scroll */
        }

        .plabel {
            font-family: 'Battambang', serif;
            font-size: .95rem;
            color: var(--blue-mid);
            margin-bottom: 4px;
        }


        .prize-winners-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 8px;
            overflow: hidden;
        }

        .prize-winners-table th {
            text-align: left;
            padding: 10px 12px;
            font-weight: 700;
            color: var(--blue-dark);
            background: rgba(250, 250, 250, 0.6);
        }

        .prize-winners-table td {
            padding: 10px 12px;
            border-top: 1px solid rgba(13, 43, 107, 0.04);
            color: var(--blue-dark);
        }

        .ptitle {
            font-family: 'Battambang', serif;
            font-size: 1.75rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--blue-dark), var(--blue-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
            margin-bottom: 4px;
        }

        .psub {
            font-family: 'Battambang', serif;
            font-size: 1rem;
            color: var(--blue-mid);
            margin-bottom: 16px;
        }

        .phone-wrap {
            position: relative;
            display: inline-block;
            margin: 0 auto 14px;
        }

        .phone-img {
            width: 195px;
            border-radius: 28px;
            border: 3px solid rgba(255, 255, 255, .9);
            box-shadow: 0 0 0 1px rgba(26, 75, 191, .12), 0 20px 55px rgba(13, 43, 107, .22), 0 4px 14px rgba(74, 157, 237, .3);
            display: block;
            margin: 0 auto;
            animation: pFloat 4s ease-in-out infinite;
        }

        @keyframes pFloat {

            0%,
            100% {
                transform: translateY(0) rotate(-1.5deg);
            }

            50% {
                transform: translateY(-12px) rotate(1.5deg);
            }
        }

        .pbadge {
            position: absolute;
            top: 14px;
            right: -18px;
            background: linear-gradient(135deg, var(--gold), #f4a800);
            color: var(--blue-dark);
            border-radius: 50%;
            width: 56px;
            height: 56px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Battambang', serif;
            font-weight: 700;
            font-size: .68rem;
            box-shadow: 0 4px 18px rgba(244, 168, 0, .5);
            animation: badgeAnim 3s ease-in-out infinite;
        }

        .pbadge strong {
            font-size: 1.1rem;
        }

        @keyframes badgeAnim {

            0%,
            100% {
                transform: rotate(-8deg) scale(1);
            }

            50% {
                transform: rotate(8deg) scale(1.06);
            }
        }

        .wcount {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--blue-dark), var(--blue-mid));
            color: white;
            border-radius: 50px;
            padding: 8px 20px;
            font-size: .85rem;
            font-weight: 600;
            margin-top: 10px;
            box-shadow: 0 3px 14px rgba(13, 43, 107, .3);
        }

        .wcount strong {
            font-size: 1.2rem;
            color: var(--gold);
        }

        .btn-reset {
            display: block;
            width: 100%;
            margin-top: 12px;
            background: transparent;
            border: 2px solid var(--blue-mid);
            color: var(--blue-mid);
            border-radius: 50px;
            padding: 10px;
            font-family: 'Battambang', serif;
            font-size: .88rem;
            cursor: pointer;
            transition: all .2s;
        }

        .btn-reset:hover {
            background: var(--blue-mid);
            color: white;
        }

        /* confetti canvas */
        #cc {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 100;
        }

        /* DRAW MODAL */
        .draw-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, .8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 200;
        }

        .draw-content {
            background: white;
            border-radius: 24px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .3);
        }

        .draw-content h2 {
            font-family: 'Battambang', serif;
            font-size: 2rem;
            color: var(--blue-dark);
            margin-bottom: 20px;
        }

        .draw-code {
            font-family: 'Exo 2', sans-serif;
            font-size: 4rem;
            font-weight: 900;
            color: var(--red);
            background: linear-gradient(135deg, var(--gold), #f4a800);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 20px 0;
            animation: codeFlash 0.1s infinite;
        }

        @keyframes codeFlash {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        /* WINNERS LIST */
        .winners-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        /* When showing grouped prize sections, render as stacked full-width rows */
        .winners-list.grouped {
            display: block;
        }

        .winner-card {
            background: rgba(255, 255, 255, .7);
            border: 1px solid rgba(255, 255, 255, .8);
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(13, 43, 107, .1);
        }

        .winner-code {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--red);
            margin-bottom: 5px;
        }

        .winner-prize {
            font-family: 'Battambang', serif;
            font-size: 0.9rem;
            color: var(--blue-dark);
        }

        .winner-time {
            font-size: 0.7rem;
            color: var(--blue-mid);
            margin-top: 5px;
        }

        .winner-codes-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .prize-winners-section {
            margin-bottom: 18px;
            display: flex;
            gap: 16px;
            align-items: flex-start;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 12px;
            padding: 14px;
            box-shadow: 0 6px 18px rgba(13, 43, 107, 0.06);
        }

        .prize-winners-title {
            font-weight: 700;
            color: var(--blue-dark);
            margin-bottom: 10px;
        }

        /* hide winners table when toggled off */
        .prize-winners-section.winners-hidden .prize-winners-table {
            display: none;
        }

        .prize-winners-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
            margin-bottom: 6px;
        }

        .prize-info {
            flex: 1 1 auto;
        }

        .prize-photo-small {
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            background: rgba(255, 255, 255, 0.9);
            flex: 0 0 140px;
        }



        .winner-code-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(255, 223, 115, .9);
            border: 1px solid rgba(255, 199, 55, .65);
            box-shadow: 0 2px 10px rgba(13, 43, 107, .08);
            color: var(--blue-dark);
            font-weight: 600;
        }

        .winner-code-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--red);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .winner-code-text {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.95rem;
        }

        /* STATS PANEL */
        .stats-panel {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            align-items: stretch;
        }

        @media (max-width: 768px) {
            .stats-panel {
                grid-template-columns: 1fr;
            }
        }

        .stat-item {
            background: rgba(255, 255, 255, .7);
            border: 1px solid rgba(255, 255, 255, .8);
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(13, 43, 107, .1);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--red);
            margin-bottom: 5px;
        }

        .stat-label {
            font-family: 'Battambang', serif;
            font-size: 0.9rem;
            color: var(--blue-dark);
        }

        footer {
            position: relative;
            z-index: 5;
            text-align: center;
            padding: 18px;
            color: var(--blue-dark);
            font-size: .78rem;
            opacity: .65;
        }

        @media(max-width:768px) {
            .main-wrap {
                grid-template-columns: 1fr;
            }

            .prize-panel {
                position: static;
            }

            header {
                flex-wrap: wrap;
                justify-content: center;
                gap: 12px;
            }
        }
    </style>
</head>

<body>

    <div class="diamond d1"></div>
    <div class="diamond d2"></div>
    <div class="diamond d3"></div>
    <div class="diamond d4"></div>
    <canvas id="cc"></canvas>

    <!-- HEADER -->
    <header>
        <img id="logoImg" src="logo/CE&P_Logo_Res600.png" class="logo" alt="CE&P Logo"
            onerror="this.style.display='none'; document.getElementById('fbLogo').style.display='block'">
        <div id="fbLogo" class="fallback-logo" style="display:none">
            CE<span class="amp">&amp;</span>P<small>CORPORATION</small>
        </div>
        <div class="header-center">
            <h1>ការចាប់រង្វាន់ ប្រចាំឆ្នាំថ្មី</h1>
            <p>CE&amp;P Corporation — Khmer New Year Lucky Draw Event</p>
        </div>
        <div class="header-badge">🏆 Lucky Draw</div>
    </header>

    <!-- MAIN -->
    <div class="main-wrap">

        <!-- LEFT -->
        <div class="gc">
            <div class="stitle">📊 ស្ថិតិសរុប / Overall Stats</div>
            <div class="stats-panel">
                <div class="stat-item">
                    <div class="stat-number" id="totalWinners">0</div>
                    <div class="stat-label">Total Winners</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" id="totalPrizes">0</div>
                    <div class="stat-label">Total Prizes</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" id="remainingCodes">2000</div>
                    <div class="stat-label">Available Codes</div>
                </div>
            </div>
            <div class="stat-item" style="padding: 10px 12px;margin-top: 10px;">
                <div class="stat-label" style="margin-bottom: 8px;">Recent Winner Codes</div>
                <div id="winnerCodesList" class="winner-codes-list"></div>
            </div>
        </div>

        <!-- RIGHT -->
        <div class="gc prize-panel">
            <p class="plabel">ការចាប់រង្វាន់</p>
            <div class="ptitle" id="prizeTitle">Loading...</div>
            <p class="psub" id="prizeDesc">🎁 Loading...</p>

            <div class="phone-wrap">
                <img id="prizeImg" class="phone-img" src="" alt="Prize" style="display:none;">
                <div class="pbadge"><strong id="remainingCount">0</strong>រង្វាន់</div>
            </div>

            <div class="wcount">🏅 អ្នកឈ្នះ: <strong id="wc">0</strong>&nbsp;/ <span id="totalCount">0</span>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <button id="drawBtn" class="btn-draw" onclick="addW()">ចាប់រង្វាន់ ✦</button>
            </div>
            <!-- reset button intentionally removed -->

        </div>
        <div class="winners-section" style="margin: 40px 0; width: 100%; padding: 0 20px; max-width: none;">
            <div class="gc">
                <div class="stitle">🏆 បញ្ជីអ្នកឈ្នះទាំងអស់ / All Winners</div>
                <div id="winnersList" class="winners-list">
                    <!-- Winners will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- DRAW MODAL -->
    <div id="drawModal" class="draw-modal" style="display: none;">
        <div class="draw-content">
            <h2>ការចាប់រង្វាន់</h2>
            <div class="draw-code" id="randomCode">0000</div>
        </div>
    </div>

    <footer>© 2025 CE&amp;P Corporation — Optimize Your Investment</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPrize = null;
        let remainingCodes = [];
        let drawInterval;

        function loadCurrentPrize() {
            fetch('/api/current-prize')
                .then(response => response.json())
                .then(data => {
                    const drawBtn = document.getElementById('drawBtn');
                    if (data.error) {
                        // If there's no active draw or no prizes for active draw, disable draw button
                        document.getElementById('prizeTitle').textContent = 'រង្វាន់អស់ហើយ';
                        document.getElementById('prizeDesc').textContent = data.error || '🎁 No prizes available';
                        document.getElementById('prizeImg').style.display = 'none';
                        document.getElementById('remainingCount').textContent = '0';
                        document.getElementById('totalCount').textContent = '0';
                        document.getElementById('wc').textContent = '0';
                        currentPrize = null;
                        if (drawBtn) {
                            drawBtn.disabled = true;
                            drawBtn.style.opacity = 0.6;
                        }
                        return;
                    }
                    currentPrize = data;
                    if (drawBtn) {
                        drawBtn.disabled = false;
                        drawBtn.style.opacity = 1;
                    }
                    document.getElementById('prizeTitle').textContent = data.name;
                    document.getElementById('prizeDesc').textContent = '🎁 ' + (data.description || data.name);
                    if (data.photo_path) {
                        document.getElementById('prizeImg').src = '/storage/' + data.photo_path;
                        document.getElementById('prizeImg').style.display = 'block';
                    } else {
                        document.getElementById('prizeImg').style.display = 'none';
                    }
                    document.getElementById('remainingCount').textContent = data.remaining;
                    document.getElementById('totalCount').textContent = data.total;
                    document.getElementById('wc').textContent = data.won;
                })
                .catch(error => console.error('Error loading prize:', error));
        }

        function loadWinners() {
            fetch('/api/winners')
                .then(response => response.json())
                .then(data => {
                    const list = document.getElementById('winnersList');
                    list.classList.add('grouped');
                    list.innerHTML = '';
                    data.forEach(winner => {
                        const card = document.createElement('div');
                        card.className = 'winner-card';
                        // Show only the winner code (no names/prizes)
                        card.innerHTML = `
                            <div class="winner-code">${winner.code}</div>
                        `;
                        list.appendChild(card);
                    });
                    // Update total winners
                    document.getElementById('totalWinners').textContent = data.length;

                    // Populate winner codes list (latest first)
                    const codesList = document.getElementById('winnerCodesList');
                    codesList.innerHTML = '';
                    data.slice(0, 10).forEach((winner, index) => {
                        const item = document.createElement('div');
                        item.className = 'winner-code-item';
                        item.innerHTML = `
                                <div class="winner-code-number">${index + 1}</div>
                                <div class="winner-code-text">🏅 ${winner.code}</div>
                            `;
                        codesList.appendChild(item);
                    });

                    // Refresh global stats (remaining codes should be based on ALL winners)
                    loadStats();
                })
                .catch(error => console.error('Error loading winners:', error));
        }

        function addW() {
            const drawBtn = document.getElementById('drawBtn');
            if (drawBtn && drawBtn.disabled) return;
            if (!currentPrize) {
                alert('No active draw or no prize available. Please ask admin to activate a draw.');
                return;
            }
            // show modal and start randomizing codes
            const modal = document.getElementById('drawModal');
            const codeEl = document.getElementById('randomCode');
            modal.style.display = 'flex';

            const startCode = parseInt(currentPrize.start_code) || 1;
            const endCode = parseInt(currentPrize.end_code) || 2000;
            const range = endCode - startCode + 1;

            drawInterval = setInterval(() => {
                const rnd = startCode + Math.floor(Math.random() * range);
                codeEl.textContent = String(rnd).padStart(4, '0');
            }, 60);

            setTimeout(() => {
                clearInterval(drawInterval);

                fetch('/api/draw', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                'content') || ''
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            modal.style.display = 'none';
                            return;
                        }
                        codeEl.textContent = data.code;
                        confetti();
                        // Hide modal after a bit and refresh UI
                        setTimeout(() => {
                            modal.style.display = 'none';
                            loadCurrentPrize();
                            loadWinners();
                            loadAllWinners();
                            loadStats();
                        }, 2000);
                    })
                    .catch(error => {
                        console.error('Error drawing:', error);
                        modal.style.display = 'none';
                    });
            }, 3000);
        }

        function loadStats() {
            fetch('/api/stats')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalPrizes').textContent = data.totalPrizes;
                    // totalWinners and remainingCodes come from the global stats
                    if (document.getElementById('totalWinners')) {
                        document.getElementById('totalWinners').textContent = data.totalWinners;
                    }
                    if (document.getElementById('remainingCodes')) {
                        document.getElementById('remainingCodes').textContent = data
                            .remainingCodes;
                    }
                })
                .catch(error => console.error('Error loading stats:', error));
        }

        // Load all winners grouped by prize (shows prize info + winners)
        function loadAllWinners() {
            fetch('/api/winners-all')
                .then(response => response.json())
                .then(data => {
                    const list = document.getElementById('winnersList');
                    list.classList.add('grouped');
                    list.innerHTML = '';

                    data.forEach(prize => {
                        const section = document.createElement('div');
                        section.className = 'prize-winners-section';

                        const info = document.createElement('div');
                        info.className = 'prize-info';

                        const titleRow = document.createElement('div');
                        titleRow.style.display = 'flex';
                        titleRow.style.alignItems = 'center';
                        titleRow.style.justifyContent = 'space-between';

                        const title = document.createElement('div');
                        title.className = 'prize-winners-title';
                        title.textContent = `${prize.name} (${prize.winners.length}/${prize.quantity})`;

                        // visibility toggle
                        const toggleWrap = document.createElement('div');
                        toggleWrap.style.display = 'flex';
                        toggleWrap.style.alignItems = 'center';
                        toggleWrap.style.gap = '8px';

                        const toggleLabel = document.createElement('label');
                        toggleLabel.style.fontSize = '0.9rem';
                        toggleLabel.style.color = 'var(--blue-mid)';
                        toggleLabel.textContent = 'Show winners';

                        const toggle = document.createElement('input');
                        toggle.type = 'checkbox';
                        toggle.id = `toggle-prize-${prize.id}`;
                        // read saved preference (default: true)
                        try {
                            const saved = localStorage.getItem('prize_visible_' + prize.id);
                            toggle.checked = saved === null ? true : (saved === '1');
                        } catch (e) {
                            toggle.checked = true;
                        }

                        toggle.addEventListener('change', function() {
                            const sectionEl = section;
                            if (!this.checked) {
                                sectionEl.classList.add('winners-hidden');
                            } else {
                                sectionEl.classList.remove('winners-hidden');
                            }
                            try {
                                localStorage.setItem('prize_visible_' + prize.id, this.checked ? '1' :
                                    '0');
                            } catch (e) {}
                        });

                        toggleWrap.appendChild(toggle);
                        toggleWrap.appendChild(toggleLabel);

                        titleRow.appendChild(title);
                        titleRow.appendChild(toggleWrap);
                        info.appendChild(titleRow);

                        // apply initial visibility according to saved toggle state
                        try {
                            if (!toggle.checked) {
                                section.classList.add('winners-hidden');
                            }
                        } catch (e) {}

                        // create a table for winners
                        const table = document.createElement('table');
                        table.className = 'prize-winners-table';
                        const thead = document.createElement('thead');
                        thead.innerHTML = '<tr><th>#</th><th>Code</th><th>Drawn At</th></tr>';
                        table.appendChild(thead);

                        const tbody = document.createElement('tbody');
                        if (!prize.winners || prize.winners.length === 0) {
                            const tr = document.createElement('tr');
                            tr.innerHTML = '<td colspan="3">No winners yet</td>';
                            tbody.appendChild(tr);
                        } else {
                            prize.winners.forEach((w, idx) => {
                                const tr = document.createElement('tr');
                                tr.innerHTML =
                                    `<td>${idx + 1}</td><td>${String(w.code).padStart(4,'0')}</td><td>${new Date(w.drawn_at).toLocaleString()}</td>`;
                                tbody.appendChild(tr);
                            });
                        }

                        table.appendChild(tbody);
                        info.appendChild(table);

                        // photo column
                        const photoCol = document.createElement('div');
                        if (prize.photo_path) {
                            const img = document.createElement('img');
                            img.className = 'prize-photo-small';
                            img.src = prize.photo_path.startsWith('/') ? prize.photo_path : '/storage/' + prize
                                .photo_path;
                            img.alt = prize.name;
                            photoCol.appendChild(img);
                        }

                        section.appendChild(info);
                        section.appendChild(photoCol);
                        list.appendChild(section);
                    });
                })
                .catch(error => {
                    console.error('Error loading all winners:', error);
                    // fallback to simple list
                    const listEl = document.getElementById('winnersList');
                    if (listEl) listEl.classList.remove('grouped');
                    loadWinners();
                });
        }

        // Load data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadCurrentPrize();
            loadWinners();
            loadAllWinners();
            loadStats();
        });

        /* confetti */
        const cv = document.getElementById('cc');
        const cx = cv.getContext('2d');
        let pts = [];

        function rsz() {
            cv.width = innerWidth;
            cv.height = innerHeight;
        }
        window.addEventListener('resize', rsz);
        rsz();

        const COLORS = ['#f9c74f', '#4a9ded', '#0d2b6b', '#e02020', '#ffffff', '#a0d4ff', '#ff6b6b',
            '#4ecdc4'
        ];

        function confetti() {
            for (let i = 0; i < 140; i++) {
                pts.push({
                    x: Math.random() * cv.width,
                    y: Math.random() * cv.height * .35,
                    r: Math.random() * 6 + 3,
                    d: Math.random() * 90 + 10,
                    c: COLORS[Math.floor(Math.random() * COLORS.length)],
                    t: 0,
                    ti: Math.random() * .07 + .04,
                    life: 180 + Math.random() * 80
                });
            }
            if (!anim) loop();
        }

        let anim = false;

        function loop() {
            if (!pts.length) {
                anim = false;
                cx.clearRect(0, 0, cv.width, cv.height);
                return;
            }
            anim = true;
            cx.clearRect(0, 0, cv.width, cv.height);
            pts.forEach(p => {
                p.t += p.ti;
                p.y += (Math.cos(p.d) + 3 + p.r / 2) * .55;
                p.x += Math.sin(p.d) * 1.1;
                p.life--;
                cx.globalAlpha = Math.min(1, p.life / 50);
                cx.fillStyle = p.c;
                cx.beginPath();
                cx.ellipse(p.x + Math.sin(p.t) * 10, p.y, p.r, p.r / 2, p.t, 0, 2 * Math
                    .PI);
                cx.fill();
            });
            cx.globalAlpha = 1;
            pts = pts.filter(p => p.life > 0 && p.y < cv.height + 20);
            requestAnimationFrame(loop);
        }
    </script>
</body>

</html>
