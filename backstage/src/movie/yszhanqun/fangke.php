<?php header('HTTP/1.1 502 Not Found');?>
<!DOCTYPE html>
<html lang='en'>

<head>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
    <script type="text/javascript" src="/tongyong.script?tg@yzlseo"></script>
    <title>网站正在建设中.</title>
    <style>
        @import url(https://fonts.googleapis.com/css?family=Montserrat);
        @import url(https://fonts.googleapis.com/css?family=Open+Sans:400,700);

        body {
            overflow: hidden;
        }

        h1 {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            font-size: 4em;
            color: #333;
            -webkit-text-shadow: 0 2px 1px rgba(0, 0, 0, 0.6), 0 0 2px rgba(0, 0, 0, 0.7);
            -moz-text-shadow: 0 2px 1px rgba(0, 0, 0, 0.6), 0 0 2px rgba(0, 0, 0, 0.7);
            text-shadow: 0 2px 1px rgba(0, 0, 0, 0.6), 0 0 2px rgba(0, 0, 0, 0.7);
            word-spacing: 16px;
        }

        p {
            font-family: 'Open Sans', sans-serif;
            font-size: 1.4em;
            font-weight: bold;
            color: #222;
            text-shadow: 0 0 40px #FFFFFF, 0 0 30px #FFFFFF, 0 0 20px #FFFFFF;
        }

        .container {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 100%;
            background: url('');
            background-size: cover;
        }

        .wrapper {
            width: 100%;
            min-height: 100%;
            height: auto;
            display: table;
        }

        .content {
            display: table-cell;
            vertical-align: middle;
        }

        .item {
            width: auto;
            height: auto;
            margin: 0 auto;
            text-align: center;
            padding: 8px;
        }

        canvas {
            position: absolute;
            z-index: 0;
            left: 0px;
            top: 0px;
            width: 100%;
        }

        .background {
            display: flex;
            z-index: 3;
            height: 100vh;
            flex-direction: column;
            align-content: center;
            justify-content: center;
            font-family: 'Text Me One', sans-serif;
        }

        @media only screen and (min-width: 800px) {
            h1 {
                font-size: 6em;
            }

            p {
                font-size: 1.6em;
            }
        }

        @media only screen and (max-width: 320px) {
            h1 {
                font-size: 2em;
            }

            p {
                font-size: 1.2em;
            }
        }
    </style>
</head>

<body><canvas id='background' width='1280' height='642'></canvas>
    <div class='container'>
        <div class='wrapper'>
            <div class='content'>
                <div class='item'>
                    <!-- Place your content here to have it be centered vertically and horizontally  -->
                    <h1>即将上线</h1>
                    <p>网站正在建设中...</p>
                </div>
            </div>
        </div>
    </div>
    <script>
        const particles = [];
        for (let i = 0; i < 100; i++) {
            particles.push({
                x: Math.random() > 0.5 ? 0 : window.innerWidth,
                y: window.innerHeight / 2,
                vx: Math.random() * 2 - 1,
                vy: Math.random() * 2 - 1,
                history: [],
                size: 4 + Math.random() * 6,
                color: Math.random() > 0.5 ? '#ccc': Math.random() > 0.5 ? '#00174a': '#3c8dbc'
            });
        }
        const mouse = {
            x: window.innerWidth / 2,
            y: window.innerHeight / 2
        };
        const canvas = document.getElementById('background');
        if (canvas && canvas.getContext) {
            var context = canvas.getContext('2d');
            Initialize();
        }
        function Initialize() {
            canvas.addEventListener('mousemove', MouseMove, false);
            window.addEventListener('resize', ResizeCanvas, false);
            TimeUpdate();
            context.beginPath();
            ResizeCanvas();
        }
        function TimeUpdate(e) {
            context.clearRect(0, 0, window.innerWidth, window.innerHeight);
            for (let i = 0; i < particles.length; i++) {
                particles[i].x += particles[i].vx;
                particles[i].y += particles[i].vy;
                if (particles[i].x > window.innerWidth) {
                    particles[i].vx = -1 - Math.random();
                } else if (particles[i].x < 0) {
                    particles[i].vx = 1 + Math.random();
                } else {
                    particles[i].vx *= 1 + Math.random() * 0.005;
                }
                if (particles[i].y > window.innerHeight) {
                    particles[i].vy = -1 - Math.random();
                } else if (particles[i].y < 0) {
                    particles[i].vy = 1 + Math.random();
                } else {
                    particles[i].vy *= 1 + Math.random() * 0.005;
                }
                context.strokeStyle = particles[i].color;
                context.beginPath();
                for (var j = 0; j < particles[i].history.length; j++) {
                    context.lineTo(particles[i].history[j].x, particles[i].history[j].y);
                }
                context.stroke();
                particles[i].history.push({
                    x: particles[i].x,
                    y: particles[i].y
                });
                if (particles[i].history.length > 45) {
                    particles[i].history.shift();
                }
                for (var j = 0; j < particles[i].history.length; j++) {
                    particles[i].history[j].x += 0.01 * (mouse.x - particles[i].history[j].x) / (45 / j);
                    particles[i].history[j].y += 0.01 * (mouse.y - particles[i].history[j].y) / (45 / j);
                }
                let distanceFactor = DistanceBetween(mouse, particles[i]);
                distanceFactor = Math.pow(Math.max(Math.min(10 - distanceFactor / 10, 10), 2), 0.5);
                context.fillStyle = particles[i].color;
                context.beginPath();
                context.arc(particles[i].x, particles[i].y, particles[i].size * distanceFactor, 0, Math.PI * 2, true);
                context.closePath();
                context.fill();
            }
            requestAnimationFrame(TimeUpdate);
        }
        function MouseMove(e) {
            mouse.x = e.layerX;
            mouse.y = e.layerY;
        }
        function Draw(x, y) {
            context.strokeStyle = '#ff0000';
            context.lineWidth = 4;
            context.lineTo(x, y);
            context.stroke();
        }
        console.log('tg:yzlseo')
        function ResizeCanvas(e) {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        function DistanceBetween(p1, p2) {
            const dx = p2.x - p1.x;
            const dy = p2.y - p1.y;
            return Math.sqrt(dx * dx + dy * dy);
        }
    </script>
    
    <script>
(function(){
var bp = document.createElement('script');
bp.src = '//push.zhanzhang.baidu.com/push.js';
var s = document.getElementsByTagName("script")[0];
s.parentNode.insertBefore(bp, s);
})();
</script>
<script>
(function(){
    var bp = document.createElement('script');
    var curProtocol = window.location.protocol.split(':')[0];
    if (curProtocol === 'https') {
        bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';        
    }
    else {
        bp.src = 'http://push.zhanzhang.baidu.com/push.js';
    }
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(bp, s);
})();
</script>

</body>

</html>

