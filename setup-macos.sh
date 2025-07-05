#!/bin/bash

# macOS での開発環境セットアップスクリプト

echo "🍎 macOS iOS開発環境セットアップ開始"

# Homebrew のインストール
echo "📦 Homebrew をインストール中..."
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Node.js のインストール
echo "📦 Node.js をインストール中..."
brew install node

# Git のインストール
echo "📦 Git をインストール中..."
brew install git

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
brew install --cask docker

# Visual Studio Code のインストール
echo "💻 VS Code をインストール中..."
brew install --cask visual-studio-code

# Xcode Command Line Tools のインストール
echo "🔧 Xcode Command Line Tools をインストール中..."
xcode-select --install

echo "✅ 基本ツールのインストール完了"

# Capacitor CLI のインストール
echo "📱 Capacitor CLI をインストール中..."
npm install -g @capacitor/cli

# iOS開発用の追加ツール
echo "🍎 iOS開発ツールをインストール中..."
brew install --cask xcode
brew install ios-deploy
brew install cocoapods

echo "🎉 macOS iOS開発環境セットアップ完了！"

echo "📋 次のステップ:"
echo "1. App Store から Xcode をインストール（または更新）"
echo "2. Apple Developer Account にサインアップ"
echo "3. GitHub/GitLab でリポジトリを作成"
echo "4. 'git clone <repository-url>' でプロジェクトをクローン"
echo "5. 'npx cap open ios' でXcodeプロジェクトを開く"
echo ""
echo "🔑 SSH キーの設定（推奨）:"
echo "ssh-keygen -t ed25519 -C \"$git_email\""
echo "cat ~/.ssh/id_ed25519.pub"
echo "👆 この公開鍵をGitHub/GitLabに登録してください"
