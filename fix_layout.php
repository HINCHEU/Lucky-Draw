<?php

$file = 'resources/views/welcome.blade.php';
$content = file_get_contents($file);

// Replace LEFT column with flex container
$leftPattern = '/        <!-- LEFT -->\s*<div class="gc">\s*<div class="stitle">.*?Recent Winners<\/div>.*?<\/div>\s*<\/div>/s';
$leftReplacement = '        <!-- LEFT -->
        <div style="display: flex; flex-direction: column; gap: 28px;">
            <!-- Recent Winners Card -->
            <div class="gc">
                <div class="stitle">📋 Recent Winners</div>
                <div class="stat-item" style="padding: 10px 12px;margin-top: 10px;">
                    <div class="stat-label" style="margin-bottom: 8px;">Latest Winners</div>
                    <div id="winnerCodesList" class="winner-codes-list"></div>
                </div>
            </div>

            <!-- All Winners Card -->
            <div class="gc">
                <div class="stitle">🏆 «អ្នកឈ្នះទាំងअས់ / All Winners</div>
                <div id="winnersList" class="winners-list">
                    <!-- Winners will be loaded here -->
                </div>
            </div>
        </div>';

$content = preg_replace($leftPattern, $leftReplacement, $content);

// Wrap RIGHT column in flex container
$rightPattern = '/        <!-- RIGHT -->\s*<div class="gc prize-panel">/s';
$rightReplacement = '        <!-- RIGHT -->
        <div style="display: flex; flex-direction: column; gap: 28px;">
            <div class="gc prize-panel">';

$content = preg_replace($rightPattern, $rightReplacement, $content);

// Fix closing divs
$content = str_replace('        </div>
        </div>
    </div>
    <!-- Confirm Modal -->', '        </div>
        </div>
        </div>
    </div>
    <!-- Confirm Modal -->', $content);

file_put_contents($file, $content);
echo "Layout fully updated successfully\n";
?>