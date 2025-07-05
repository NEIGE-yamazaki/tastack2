<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Capacitor Mobile Test - TASTACK2</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 p-4">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">📱 モバイル機能テスト</h1>
        
        <div class="space-y-4">
            <!-- デバイス情報取得 -->
            <button id="getDeviceInfo" class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition-colors">
                📋 デバイス情報を取得
            </button>
            <div id="deviceInfo" class="hidden bg-gray-50 p-3 rounded text-sm"></div>

            <!-- ハプティクスフィードバック -->
            <button id="triggerHaptics" class="w-full bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition-colors">
                📳 バイブレーション
            </button>

            <!-- トースト表示 -->
            <button id="showToast" class="w-full bg-orange-500 text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition-colors">
                🍞 トースト表示
            </button>

            <!-- ステータスバー制御 -->
            <button id="toggleStatusBar" class="w-full bg-purple-500 text-white py-2 px-4 rounded-lg hover:bg-purple-600 transition-colors">
                📱 ステータスバー切替
            </button>

            <!-- カメラアクセス -->
            <button id="openCamera" class="w-full bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition-colors">
                📷 カメラを開く
            </button>
            <div id="photoPreview" class="hidden">
                <img id="photoImg" class="w-full rounded-lg mt-2" />
            </div>

            <!-- 位置情報取得 -->
            <button id="getLocation" class="w-full bg-indigo-500 text-white py-2 px-4 rounded-lg hover:bg-indigo-600 transition-colors">
                📍 位置情報を取得
            </button>
            <div id="locationInfo" class="hidden bg-gray-50 p-3 rounded text-sm"></div>

            <!-- ブラウザで外部URL開く -->
            <button id="openBrowser" class="w-full bg-teal-500 text-white py-2 px-4 rounded-lg hover:bg-teal-600 transition-colors">
                🌐 外部ブラウザで開く
            </button>
        </div>

        <div class="mt-6 p-3 bg-blue-50 rounded-lg text-sm">
            <h3 class="font-bold text-blue-800">💡 テスト方法</h3>
            <ul class="text-blue-700 mt-2 space-y-1">
                <li>• ブラウザ: F12 → デバイスツールバー</li>
                <li>• 実機: APKインストール後テスト</li>
                <li>• エミュレータ: Android Studioから実行</li>
            </ul>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Capacitorがロードされているか確認
            if (typeof window.Capacitor === 'undefined') {
                console.log('Web環境でのテスト - Capacitorプラグインは制限されます');
            }

            // デバイス情報取得
            document.getElementById('getDeviceInfo').addEventListener('click', async function() {
                try {
                    if (window.CapacitorCustom && window.CapacitorCustom.getDeviceInfo) {
                        const info = await window.CapacitorCustom.getDeviceInfo();
                        document.getElementById('deviceInfo').innerHTML = `
                            <strong>デバイス情報:</strong><br>
                            モデル: ${info.model}<br>
                            プラットフォーム: ${info.platform}<br>
                            OS: ${info.operatingSystem}<br>
                            バージョン: ${info.osVersion}<br>
                            Web環境: ${info.isWeb ? 'はい' : 'いいえ'}
                        `;
                        document.getElementById('deviceInfo').classList.remove('hidden');
                    } else {
                        throw new Error('デバイス情報の取得に失敗しました');
                    }
                } catch (error) {
                    alert('デバイス情報の取得に失敗: ' + error.message);
                }
            });

            // ハプティクス
            document.getElementById('triggerHaptics').addEventListener('click', async function() {
                try {
                    if (window.CapacitorCustom && window.CapacitorCustom.vibrate) {
                        await window.CapacitorCustom.vibrate();
                        alert('バイブレーションを実行しました！');
                    } else {
                        alert('Web環境ではバイブレーションは利用できません');
                    }
                } catch (error) {
                    alert('バイブレーションに失敗: ' + error.message);
                }
            });

            // トースト
            document.getElementById('showToast').addEventListener('click', async function() {
                try {
                    if (window.CapacitorCustom && window.CapacitorCustom.showToast) {
                        await window.CapacitorCustom.showToast('こんにちは、TASTACK2！');
                    } else {
                        alert('こんにちは、TASTACK2！（Web環境）');
                    }
                } catch (error) {
                    alert('トースト表示に失敗: ' + error.message);
                }
            });

            // ステータスバー
            document.getElementById('toggleStatusBar').addEventListener('click', async function() {
                try {
                    if (window.CapacitorCustom && window.CapacitorCustom.toggleStatusBar) {
                        await window.CapacitorCustom.toggleStatusBar();
                        alert('ステータスバーを切り替えました');
                    } else {
                        alert('Web環境ではステータスバー制御は利用できません');
                    }
                } catch (error) {
                    alert('ステータスバー制御に失敗: ' + error.message);
                }
            });

            // カメラ
            document.getElementById('openCamera').addEventListener('click', async function() {
                try {
                    if (window.CapacitorCustom && window.CapacitorCustom.takePicture) {
                        const image = await window.CapacitorCustom.takePicture();
                        document.getElementById('photoImg').src = image.webPath;
                        document.getElementById('photoPreview').classList.remove('hidden');
                    } else {
                        alert('Web環境ではカメラアクセスは制限されます');
                    }
                } catch (error) {
                    alert('カメラアクセスに失敗: ' + error.message);
                }
            });

            // 位置情報
            document.getElementById('getLocation').addEventListener('click', async function() {
                try {
                    if (window.CapacitorCustom && window.CapacitorCustom.getCurrentPosition) {
                        const position = await window.CapacitorCustom.getCurrentPosition();
                        document.getElementById('locationInfo').innerHTML = `
                            <strong>現在位置:</strong><br>
                            緯度: ${position.coords.latitude}<br>
                            経度: ${position.coords.longitude}<br>
                            精度: ${position.coords.accuracy}m<br>
                            取得時刻: ${new Date(position.timestamp).toLocaleString('ja-JP')}
                        `;
                        document.getElementById('locationInfo').classList.remove('hidden');
                    } else {
                        // Web Geolocation APIをフォールバック
                        navigator.geolocation.getCurrentPosition(function(position) {
                            document.getElementById('locationInfo').innerHTML = `
                                <strong>現在位置 (Web API):</strong><br>
                                緯度: ${position.coords.latitude}<br>
                                経度: ${position.coords.longitude}<br>
                                精度: ${position.coords.accuracy}m<br>
                                取得時刻: ${new Date(position.timestamp).toLocaleString('ja-JP')}
                            `;
                            document.getElementById('locationInfo').classList.remove('hidden');
                        });
                    }
                } catch (error) {
                    alert('位置情報の取得に失敗: ' + error.message);
                }
            });

            // 外部ブラウザ
            document.getElementById('openBrowser').addEventListener('click', async function() {
                try {
                    if (window.CapacitorCustom && window.CapacitorCustom.openBrowser) {
                        await window.CapacitorCustom.openBrowser('https://github.com');
                    } else {
                        window.open('https://github.com', '_blank');
                    }
                } catch (error) {
                    alert('ブラウザ起動に失敗: ' + error.message);
                }
            });
        });
    </script>
</body>
</html>
