<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TASTACK2 - 開発環境デモ</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <header class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">🚀 TASTACK2</h1>
                <p class="text-xl text-gray-600">Laravel + Capacitor + Alpine.js 開発環境</p>
            </header>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                <!-- Web開発 -->
                <div class="bg-white rounded-lg shadow-lg p-6" x-data="{ expanded: false }">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-xl">🌐</span>
                        </div>
                        <h3 class="text-xl font-semibold ml-4">Web開発</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Laravel + Vite + Alpine.js</p>
                    <button @click="expanded = !expanded"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition-colors">
                        <span x-text="expanded ? '詳細を隠す' : '詳細を表示'"></span>
                    </button>
                    <div x-show="expanded" x-collapse class="mt-4">
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li>✅ Laravel 11 バックエンド</li>
                            <li>✅ Vite ホットリロード</li>
                            <li>✅ Alpine.js リアクティブUI</li>
                            <li>✅ Tailwind CSS スタイリング</li>
                        </ul>
                    </div>
                </div>

                <!-- Mobile開発 -->
                <div class="bg-white rounded-lg shadow-lg p-6" x-data="{ expanded: false }">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-xl">📱</span>
                        </div>
                        <h3 class="text-xl font-semibold ml-4">Mobile開発</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Capacitor iOS/Android</p>
                    <button @click="expanded = !expanded"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded transition-colors">
                        <span x-text="expanded ? '詳細を隠す' : '詳細を表示'"></span>
                    </button>
                    <div x-show="expanded" x-collapse class="mt-4">
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li>✅ Capacitor 7.4.1</li>
                            <li>✅ Java 21 サポート</li>
                            <li>✅ Android Studio 連携</li>
                            <li>✅ Xcode 連携</li>
                        </ul>
                    </div>
                </div>

                <!-- 開発環境 -->
                <div class="bg-white rounded-lg shadow-lg p-6" x-data="{ expanded: false }">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-xl">🛠️</span>
                        </div>
                        <h3 class="text-xl font-semibold ml-4">開発環境</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Docker + VSCode</p>
                    <button @click="expanded = !expanded"
                        class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded transition-colors">
                        <span x-text="expanded ? '詳細を隠す' : '詳細を表示'"></span>
                    </button>
                    <div x-show="expanded" x-collapse class="mt-4">
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li>✅ Docker Sail</li>
                            <li>✅ phpMyAdmin</li>
                            <li>✅ VSCode Tasks</li>
                            <li>✅ 自動化スクリプト</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 開発ワークフロー -->
            <div class="bg-white rounded-lg shadow-lg p-8 mb-12">
                <h2 class="text-2xl font-semibold mb-6 text-center">🔄 開発ワークフロー</h2>
                <div class="space-y-4">
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <div
                            class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                            1</div>
                        <div class="ml-4">
                            <h4 class="font-semibold">サーバー起動</h4>
                            <p class="text-sm text-gray-600">Laravel Sail + Vite Dev Server</p>
                        </div>
                    </div>
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <div
                            class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white font-bold">
                            2</div>
                        <div class="ml-4">
                            <h4 class="font-semibold">Web開発</h4>
                            <p class="text-sm text-gray-600">ブラウザでリアルタイム開発</p>
                        </div>
                    </div>
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <div
                            class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white font-bold">
                            3</div>
                        <div class="ml-4">
                            <h4 class="font-semibold">Mobile同期</h4>
                            <p class="text-sm text-gray-600">Capacitorでモバイルアプリ化</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 環境情報 -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-semibold mb-6 text-center">📊 環境情報</h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold mb-3">🌐 Web環境</h3>
                        <ul class="space-y-2 text-sm">
                            <li><strong>URL:</strong> <a href="http://localhost:8081"
                                    class="text-blue-500 hover:underline">http://localhost:8081</a></li>
                            <li><strong>Vite:</strong> <a href="http://localhost:5173"
                                    class="text-blue-500 hover:underline">http://localhost:5173</a></li>
                            <li><strong>phpMyAdmin:</strong> <a href="http://localhost:8080"
                                    class="text-blue-500 hover:underline">http://localhost:8080</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-3">📱 Mobile環境</h3>
                        <ul class="space-y-2 text-sm">
                            <li><strong>Java:</strong> OpenJDK 21</li>
                            <li><strong>Capacitor:</strong> 7.4.1</li>
                            <li><strong>Android SDK:</strong> 35</li>
                            <li><strong>Min SDK:</strong> 23</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- アクション -->
            <div class="mt-12 text-center">
                <div class="space-x-4">
                    <button onclick="window.location.reload()"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors">
                        🔄 ページ更新
                    </button>
                    <button onclick="window.open('http://localhost:8080', '_blank')"
                        class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition-colors">
                        🗄️ phpMyAdmin
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Alpine.js の初期化確認
        document.addEventListener('alpine:init', () => {
            console.log('Alpine.js が正常に初期化されました');
        });

        // 開発環境チェック
        console.log('TASTACK2 開発環境が正常に動作しています');
        console.log('Laravel + Capacitor + Alpine.js');
    </script>
</body>

</html>