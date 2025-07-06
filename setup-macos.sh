#!/bin/bash

# macOS での開発環境セットアップスクリプト

echo "🍎 macOS iOS開発環境セットアップ開始"

# Homebrew のパスを設定
export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH"

# Homebrew がインストールされているかチェック
if ! command -v brew &> /dev/null; then
    echo "📦 Homebrew をインストール中..."
    /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
    
    # インストール後にパスを再設定
    export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH"
    
    # シェル設定ファイルにパスを追加
    if [[ $SHELL == *"zsh"* ]]; then
        echo 'export PATH="/opt/homebrew/bin:$PATH"' >> ~/.zshrc
        source ~/.zshrc
    elif [[ $SHELL == *"bash"* ]]; then
        echo 'export PATH="/opt/homebrew/bin:$PATH"' >> ~/.bash_profile
        source ~/.bash_profile
    fi
else
    echo "✅ Homebrew は既にインストールされています"
fi

# Node.js のインストール
echo "📦 Node.js をインストール中..."
if ! command -v node &> /dev/null; then
    brew install node
else
    echo "✅ Node.js は既にインストールされています"
fi

# Git のインストール
echo "📦 Git をインストール中..."
if ! command -v git &> /dev/null; then
    brew install git
else
    echo "✅ Git は既にインストールされています"
fi

# Git の基本設定
echo "⚙️ Git を設定中..."
echo "Git ユーザー名を入力してください:"
read git_username
echo "Git メールアドレスを入力してください:"
read git_email

git config --global user.name "$git_username"
git config --global user.email "$git_email"
git config --global init.defaultBranch main
git config --global core.autocrlf input
git config --global core.editor "code --wait"

echo "✅ Git 設定完了"

# Docker Desktop for Mac のインストール
echo "🐳 Docker Desktop をインストール中..."
if ! command -v docker &> /dev/null; then
    brew install --cask docker
else
    echo "✅ Docker Desktop は既にインストールされています"
fi

# phpMyAdmin のインストール
echo "🗄️ phpMyAdmin をインストール中..."
if ! brew list phpmyadmin &> /dev/null; then
    brew install phpmyadmin
    echo "✅ phpMyAdmin インストール完了"
else
    echo "✅ phpMyAdmin は既にインストールされています"
fi

# PHP のインストール
echo "🐘 PHP をインストール中..."
if ! command -v php &> /dev/null; then
    brew install php
    echo "✅ PHP インストール完了"
else
    echo "✅ PHP は既にインストールされています"
fi

# Apache のインストール
echo "🌐 Apache をインストール中..."
if ! command -v httpd &> /dev/null; then
    brew install httpd
    echo "✅ Apache インストール完了"
else
    echo "✅ Apache は既にインストールされています"
fi

# phpMyAdmin設定の作成
echo "⚙️ phpMyAdmin設定を構成中..."
PHPMYADMIN_CONFIG="/opt/homebrew/etc/httpd/extra/phpmyadmin.conf"
if [ ! -f "$PHPMYADMIN_CONFIG" ]; then
    cat > "$PHPMYADMIN_CONFIG" << 'EOF'
# PHP設定
LoadModule php_module /opt/homebrew/opt/php/lib/httpd/modules/libphp.so

<FilesMatch \.php$>
    SetHandler application/x-httpd-php
</FilesMatch>

DirectoryIndex index.php index.html

# phpMyAdmin設定
Alias /phpmyadmin /opt/homebrew/share/phpmyadmin
<Directory /opt/homebrew/share/phpmyadmin/>
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    Require all granted
</Directory>
EOF

    # メイン設定にphpMyAdminを含める
    if ! grep -q "phpmyadmin.conf" /opt/homebrew/etc/httpd/httpd.conf; then
        echo "Include /opt/homebrew/etc/httpd/extra/phpmyadmin.conf" >> /opt/homebrew/etc/httpd/httpd.conf
    fi
    
    echo "✅ phpMyAdmin設定完了"
else
    echo "✅ phpMyAdmin設定は既に存在します"
fi

# サービスの起動
echo "🚀 サービスを起動中..."
brew services start php
brew services start httpd

echo "✅ phpMyAdmin は http://localhost:8080/phpmyadmin でアクセス可能です"

# Visual Studio Code のインストール
echo "💻 VS Code をインストール中..."
if ! command -v code &> /dev/null; then
    brew install --cask visual-studio-code
else
    echo "✅ VS Code は既にインストールされています"
fi

# Xcode Command Line Tools のインストール
echo "🔧 Xcode Command Line Tools をインストール中..."
xcode-select --install

echo "✅ 基本ツールのインストール完了"

# Capacitor CLI のインストール
echo "📱 Capacitor CLI をインストール中..."
if ! command -v cap &> /dev/null; then
    npm install -g @capacitor/cli
else
    echo "✅ Capacitor CLI は既にインストールされています"
fi

# iOS開発用の追加ツール
echo "🍎 iOS開発ツールをインストール中..."

# iOS Deploy
if ! command -v ios-deploy &> /dev/null; then
    brew install ios-deploy
else
    echo "✅ ios-deploy は既にインストールされています"
fi

# CocoaPods
if ! command -v pod &> /dev/null; then
    brew install cocoapods
else
    echo "✅ CocoaPods は既にインストールされています"
fi

# Composer のインストール
echo "📦 Composer をインストール中..."
if ! command -v composer &> /dev/null; then
    brew install composer
    echo "✅ Composer インストール完了"
else
    echo "✅ Composer は既にインストールされています"
fi

# Laravel 依存関係のインストール
echo "📚 Laravel 依存関係をインストール中..."
if [ ! -d "vendor" ]; then
    composer install
    echo "✅ Laravel 依存関係インストール完了"
else
    echo "✅ Laravel 依存関係は既にインストールされています"
fi

# Sail エイリアスの設定
echo "🚢 Sail エイリアスを設定中..."
if ! grep -q "alias sail" ~/.zshrc; then
    echo 'alias sail="./vendor/bin/sail"' >> ~/.zshrc
    echo "✅ Sail エイリアス設定完了"
else
    echo "✅ Sail エイリアスは既に設定済みです"
fi

# Xcode のインストール確認
echo "🍎 Xcode インストール状況を確認中..."
if [ -d "/Applications/Xcode.app" ]; then
    echo "✅ Xcode は既にインストールされています"
    
    # Developer Directoryの設定
    echo "🔧 Xcode Developer Directory を設定中..."
    sudo xcode-select -s /Applications/Xcode.app/Contents/Developer
    
    # Xcodeライセンスの確認
    echo "📋 Xcodeライセンスを確認中..."
    sudo xcodebuild -license accept 2>/dev/null || echo "⚠️ Xcodeライセンスの承認が必要な場合があります"
    
else
    echo "❌ Xcode がインストールされていません"
    echo "📱 App Store からXcodeをインストールしています..."
    
    # App StoreでXcodeページを開く
    open "https://apps.apple.com/jp/app/xcode/id497799835"
    
    echo "⏳ Xcodeのインストールが完了するまでお待ちください..."
    echo "   インストール完了後、このスクリプトを再実行してください"
    
    # Xcodeのインストール待機
    echo "🔄 Xcodeのインストールを待機中..."
    while [ ! -d "/Applications/Xcode.app" ]; do
        echo "   Xcodeのインストールを待っています... (30秒後に再確認)"
        sleep 30
    done
    
    echo "✅ Xcode インストール完了!"
    
    # Developer Directoryの設定
    echo "🔧 Xcode Developer Directory を設定中..."
    sudo xcode-select -s /Applications/Xcode.app/Contents/Developer
    
    # Xcodeライセンスの承認
    echo "📋 Xcodeライセンスを承認中..."
    sudo xcodebuild -license accept
fi

echo "🎉 macOS iOS開発環境セットアップ完了！"

echo "📋 次のステップ:"
echo "1. プロジェクトセットアップ: npm run setup:mac"
echo "2. 開発サーバー起動: npm run dev"
echo "3. phpMyAdmin起動: brew services start phpmyadmin"
echo "4. phpMyAdminアクセス: http://localhost:8080"
echo "5. iOS開発開始: npm run cap:open:ios"
echo ""
echo "🗄️ phpMyAdmin コマンド:"
echo "   起動: brew services start phpmyadmin"
echo "   停止: brew services stop phpmyadmin"
echo "   再起動: brew services restart phpmyadmin"
echo ""
echo "🔑 SSH キーの設定（推奨）:"
echo "ssh-keygen -t ed25519 -C \"$git_email\""
echo "cat ~/.ssh/id_ed25519.pub"
echo "👆 この公開鍵をGitHub (https://github.com/NEIGE-yamazaki/tanstack2) に登録してください"
echo ""
echo "🚀 プロジェクトクローン＆セットアップ:"
echo "git clone https://github.com/NEIGE-yamazaki/tanstack2.git"
echo "cd tanstack2"
echo "npm run setup:mac"
