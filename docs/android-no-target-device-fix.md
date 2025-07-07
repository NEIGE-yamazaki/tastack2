# Android "No target device found" エラー解決ガイド

## エラーの概要
「No target device found」エラーは、Android Studioまたはコマンドラインツールが実行対象のAndroidデバイス（エミュレータまたは実機）を見つけられない場合に発生します。

## 現在の状況確認

### 1. 接続デバイスの確認
```bash
# 接続されているデバイスを確認
adb devices

# 期待される出力例：
# List of devices attached
# emulator-5554    device    (エミュレータの場合)
# AB1C234567890    device    (実機の場合)
```

### 2. 利用可能なエミュレータの確認
```bash
# 作成済みエミュレータ（AVD）の一覧
$ANDROID_HOME/emulator/emulator -list-avds

# または Android Studio のコマンドラインツールを使用
$ANDROID_HOME/cmdline-tools/latest/bin/avdmanager list avd
```

## 解決方法

### 🎯 解決方法1: Android Studioでエミュレータを作成（推奨）

#### 手順
1. **Android Studioを起動**:
   ```bash
   npm run android:studio:ja
   ```

2. **AVD Managerを開く**:
   - Android Studioのメニューから **Tools** > **AVD Manager**
   - または上部ツールバーの📱アイコンをクリック

3. **新しいエミュレータを作成**:
   - **Create Virtual Device** をクリック
   - **Phone** カテゴリから **Pixel 4** を選択（推奨）
   - **Next** をクリック

4. **システムイメージを選択**:
   - **Recommended** タブから **API Level 30** 以上を選択
   - **Download** をクリック（未ダウンロードの場合）
   - ダウンロード完了後 **Next** をクリック

5. **設定を確認して作成**:
   - **AVD Name**: `Pixel_4_API_30` （デフォルトでOK）
   - **Advanced Settings** で以下を確認:
     - **RAM**: 2048 MB以上
     - **Internal Storage**: 2048 MB以上
   - **Finish** をクリック

6. **エミュレータを起動**:
   - AVD Manager で作成したエミュレータの **▶️** ボタンをクリック

### 🔧 解決方法2: コマンドラインでエミュレータを作成

#### 必要なコンポーネントのインストール
```bash
# Android SDK Managerでエミュレータをインストール
$ANDROID_HOME/cmdline-tools/latest/bin/sdkmanager "emulator"
$ANDROID_HOME/cmdline-tools/latest/bin/sdkmanager "system-images;android-30;google_apis;x86_64"

# AVDを作成
$ANDROID_HOME/cmdline-tools/latest/bin/avdmanager create avd \
  -n "Pixel_4_API_30" \
  -k "system-images;android-30;google_apis;x86_64" \
  -d "pixel_4"

# エミュレータを起動
$ANDROID_HOME/emulator/emulator @Pixel_4_API_30
```

### 📱 解決方法3: 実機デバイスを使用

#### 実機の設定手順
1. **開発者オプションを有効化**:
   - **設定** > **デバイス情報**
   - **ビルド番号** を7回連続タップ
   - 「デベロッパーになりました」と表示される

2. **USB デバッグを有効化**:
   - **設定** > **開発者オプション**
   - **USB デバッグ** をONにする

3. **デバイスを接続**:
   - USBケーブルでPC/Macに接続
   - 「USBデバッグを許可しますか？」→ **OK**

4. **接続確認**:
   ```bash
   adb devices
   # デバイスが "device" として表示されることを確認
   ```

## 自動解決スクリプト

### エミュレータ作成・起動スクリプト
```bash
#!/bin/bash
# scripts/setup-android-emulator.sh

echo "=== Android エミュレータ セットアップ ==="

# エミュレータの確認
echo "1. 既存のエミュレータを確認中..."
EXISTING_AVDS=$($ANDROID_HOME/cmdline-tools/latest/bin/avdmanager list avd | grep "Name:" | wc -l)

if [ "$EXISTING_AVDS" -gt 0 ]; then
    echo "   既存のエミュレータが見つかりました:"
    $ANDROID_HOME/cmdline-tools/latest/bin/avdmanager list avd | grep "Name:"
    
    echo "   最初のエミュレータを起動しますか？ [y/N]"
    read -r start_existing
    if [[ $start_existing =~ ^[Yy]$ ]]; then
        FIRST_AVD=$($ANDROID_HOME/cmdline-tools/latest/bin/avdmanager list avd | grep "Name:" | head -1 | sed 's/.*Name: //')
        echo "   エミュレータ '$FIRST_AVD' を起動中..."
        $ANDROID_HOME/emulator/emulator @$FIRST_AVD &
        exit 0
    fi
fi

echo "2. 新しいエミュレータを作成します..."

# 必要なシステムイメージの確認・インストール
echo "   システムイメージを確認中..."
if ! $ANDROID_HOME/cmdline-tools/latest/bin/sdkmanager --list | grep -q "system-images;android-30;google_apis;x86_64"; then
    echo "   システムイメージをダウンロード中..."
    $ANDROID_HOME/cmdline-tools/latest/bin/sdkmanager "system-images;android-30;google_apis;x86_64"
fi

# AVDの作成
echo "   エミュレータを作成中..."
$ANDROID_HOME/cmdline-tools/latest/bin/avdmanager create avd \
    -n "Pixel_4_API_30_Auto" \
    -k "system-images;android-30;google_apis;x86_64" \
    -d "pixel_4" \
    --force

echo "3. エミュレータを起動中..."
$ANDROID_HOME/emulator/emulator @Pixel_4_API_30_Auto &

echo "✅ エミュレータの起動が完了しました"
echo "   起動には数分かかる場合があります"
echo "   'adb devices' で接続を確認してください"
```

## トラブルシューティング

### ケース1: adb devices で何も表示されない

#### 解決方法
```bash
# ADBサーバーを再起動
adb kill-server
adb start-server

# デバイスを再確認
adb devices
```

### ケース2: エミュレータが起動しない

#### 解決方法
```bash
# Intel HAXM が有効かチェック（Intel CPUの場合）
ls /dev/kvm 2>/dev/null || echo "KVM が無効です"

# エミュレータを詳細ログ付きで起動
$ANDROID_HOME/emulator/emulator @AVD_NAME -verbose

# GPUエミュレーションを無効にして起動
$ANDROID_HOME/emulator/emulator @AVD_NAME -gpu off
```

### ケース3: 実機が認識されない

#### 解決方法
```bash
# Linux の場合：udev ルールの設定
echo 'SUBSYSTEM=="usb", ATTR{idVendor}=="18d1", MODE="0666", GROUP="plugdev"' | sudo tee /etc/udev/rules.d/51-android.rules
sudo chmod a+r /etc/udev/rules.d/51-android.rules
sudo udevadm control --reload-rules

# USB接続モードを「ファイル転送」に変更
# デバイスの通知エリアから設定変更
```

### ケース4: エミュレータの動作が重い

#### 最適化設定
```bash
# エミュレータを軽量設定で起動
$ANDROID_HOME/emulator/emulator @AVD_NAME \
    -gpu host \
    -memory 2048 \
    -cores 2 \
    -accel on
```

## VS Code タスク統合

### tasks.json に追加
```json
{
    "label": "Android: エミュレータ起動",
    "type": "shell",
    "command": "./scripts/setup-android-emulator.sh",
    "group": "build",
    "presentation": {
        "echo": true,
        "reveal": "always",
        "focus": false,
        "panel": "shared"
    }
}
```

## 最終確認

### デバイス接続後の確認手順
```bash
# 1. デバイス接続確認
adb devices

# 2. アプリのビルド・インストール
npm run android:install

# 3. アプリの実行
npm run android:run

# 4. ログ確認
npm run android:logcat
```

## 注意事項

⚠️ **重要**:
- エミュレータの初回起動は5-10分程度かかります
- 実機使用時はUSBデバッグの許可が必要です
- Windows環境では追加のUSBドライバーが必要な場合があります
- エミュレータは大量のメモリを使用します（推奨: 8GB RAM以上）

🎯 **推奨手順**:
1. Android Studioを起動
2. AVD Managerでエミュレータを作成
3. エミュレータを起動
4. `npm run android:run` でアプリを実行
