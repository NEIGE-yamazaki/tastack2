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
echo "1. App Store から Xcode をインストール（または更新）"
echo "2. Apple Developer Account にサインアップ"
echo "3. プロジェクトセットアップ: npm run setup:mac"
echo "4. iOS開発開始: npx cap open ios"
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
