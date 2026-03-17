<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, .4));
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
            position: sticky;
            top: 24px;
        }

        .plabel {
            font-family: 'Battambang', serif;
            font-size: .95rem;
            color: var(--blue-mid);
            margin-bottom: 4px;
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
        <img id="logoImg" src="CE_P_Logo.png" class="logo" alt="CE&P Logo"
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
            <div class="stitle">🎊 អ្នកឈ្នះរង្វាន់</div>
            <div class="slots" id="grid"></div>
            <div class="irow">
                <button class="btn-draw" onclick="addW()">ចាប់រង្វាន់ ✦</button>
            </div>
        </div>

        <!-- RIGHT -->
        <div class="gc prize-panel">
            <p class="plabel">ការចាប់រង្វាន់</p>
            <div class="ptitle">ចូលឆ្នាំខ្មែរ</div>
            <p class="psub">🎁 រង្វាន់ OPPO A5</p>

            <div class="phone-wrap">
                <img id="phoneImg" class="phone-img"
                    src="https://oasissmartphone.com/cdn/shop/files/oppo-a5-white-1.jpg?v=1737612547" alt="OPPO A5"
                    onerror="this.src='https://angkormeas.com/wp-content/uploads/2025/06/Oppo-A5-2025-White.jpg?v=1749105725'">
                <div class="pbadge"><strong>10</strong>រង្វាន់</div>
            </div>

            <div class="wcount">🏅 អ្នកឈ្នះ: <strong id="wc">0</strong>&nbsp;/ 10</div>
            <button class="btn-reset" onclick="resetAll()">🔄 ចាប់ឡើងវិញ / Reset</button>
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
        const TOTAL = 10;
        const MIN_CODE = 1;
        const MAX_CODE = 2000;
        let winners = JSON.parse(sessionStorage.getItem('cep_w') || '[]');
        let remainingCodes = [];
        let drawInterval;
        let finalCode;

        function save() {
            sessionStorage.setItem('cep_w', JSON.stringify(winners));
        }

        function render() {
            const g = document.getElementById('grid');
            g.innerHTML = '';
            for (let i = 1; i <= TOTAL; i++) {
                const w = winners[i - 1] || null;
                const d = document.createElement('div');
                d.className = 'slot' + (w ? ' won' : '');
                d.id = 's' + i;
                d.innerHTML = `<div class="snum">${i}</div><div class="sname">${w?'🏅 '+w:''}</div>`;
                g.appendChild(d);
            }
            document.getElementById('wc').textContent = winners.length;
        }

        function addW() {
            if (winners.length >= TOTAL) {
                alert('រង្វាន់ទាំង 10 ត្រូវបានចាប់ហើយ!');
                return;
            }
            // Get remaining codes
            remainingCodes = [];
            for (let i = MIN_CODE; i <= MAX_CODE; i++) {
                const code = i.toString().padStart(4, '0');
                if (!winners.includes(code)) {
                    remainingCodes.push(code);
                }
            }
            // Show modal
            document.getElementById('drawModal').style.display = 'flex';
            // Start random display
            drawInterval = setInterval(() => {
                const randomIndex = Math.floor(Math.random() * remainingCodes.length);
                document.getElementById('randomCode').textContent = remainingCodes[randomIndex];
            }, 50); // Change every 50ms
            // Stop after 3 seconds
            setTimeout(() => {
                clearInterval(drawInterval);
                finalCode = document.getElementById('randomCode').textContent;
                // Add to winners
                winners.push(finalCode);
                save();
                render();
                confetti();
                const el = document.getElementById('s' + winners.length);
                if (el) {
                    el.style.transform = 'scale(1.06)';
                    setTimeout(() => el.style.transform = '', 400);
                }
                // Hide modal after a bit
                setTimeout(() => {
                    document.getElementById('drawModal').style.display = 'none';
                }, 1000);
            }, 3000);
        }

        function resetAll() {
            if (!confirm('Reset all winners?')) return;
            winners = [];
            save();
            render();
        }

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

        const COLORS = ['#f9c74f', '#4a9ded', '#0d2b6b', '#e02020', '#ffffff', '#a0d4ff', '#ff6b6b', '#4ecdc4'];

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
                cx.ellipse(p.x + Math.sin(p.t) * 10, p.y, p.r, p.r / 2, p.t, 0, 2 * Math.PI);
                cx.fill();
            });
            cx.globalAlpha = 1;
            pts = pts.filter(p => p.life > 0 && p.y < cv.height + 20);
            requestAnimationFrame(loop);
        }

        render();
    </script>
</body>

</html>
