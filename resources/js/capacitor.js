import { App } from '@capacitor/app';
import { Browser } from '@capacitor/browser';
import { Camera, CameraResultType } from '@capacitor/camera';
import { Device } from '@capacitor/device';
import { Geolocation } from '@capacitor/geolocation';
import { Haptics, ImpactStyle } from '@capacitor/haptics';
import { SplashScreen } from '@capacitor/splash-screen';
import { StatusBar, Style } from '@capacitor/status-bar';
import { Toast } from '@capacitor/toast';

class TastackCapacitor {
    constructor() {
        this.init();
    }

    async init() {
        // App initialization
        await this.setupApp();

        // Register app events
        this.registerAppEvents();

        // Initialize UI
        this.initializeUI();
    }

    async setupApp() {
        try {
            // Hide splash screen
            await SplashScreen.hide();

            // Set status bar style
            if (window.Capacitor?.isNativePlatform()) {
                await StatusBar.setStyle({ style: Style.Dark });
            }

            console.log('TASTACK2 Capacitor initialized successfully');
        } catch (error) {
            console.error('Error setting up app:', error);
        }
    }

    registerAppEvents() {
        // App state change events
        App.addListener('appStateChange', ({ isActive }) => {
            console.log('App state changed. Is active?', isActive);
        });

        // App URL open events
        App.addListener('appUrlOpen', (event) => {
            console.log('App opened with URL:', event.url);
        });

        // Back button event for Android
        App.addListener('backButton', () => {
            console.log('Back button pressed');
            // Handle back button logic here
        });
    }

    initializeUI() {
        // Add mobile-specific styles
        if (window.Capacitor?.isNativePlatform()) {
            document.body.classList.add('capacitor-mobile');

            // Add platform-specific classes
            if (window.Capacitor.getPlatform() === 'ios') {
                document.body.classList.add('capacitor-ios');
            } else if (window.Capacitor.getPlatform() === 'android') {
                document.body.classList.add('capacitor-android');
            }
        }
    }

    // Utility methods for app features
    async showToast(message, duration = 'short') {
        try {
            await Toast.show({
                text: message,
                duration: duration
            });
        } catch (error) {
            console.error('Error showing toast:', error);
        }
    }

    async hapticFeedback(style = ImpactStyle.Medium) {
        try {
            await Haptics.impact({ style });
        } catch (error) {
            console.error('Error with haptic feedback:', error);
        }
    }

    async openExternalUrl(url) {
        try {
            await Browser.open({ url });
        } catch (error) {
            console.error('Error opening external URL:', error);
        }
    }

    async takePicture() {
        try {
            const image = await Camera.getPhoto({
                quality: 90,
                allowEditing: true,
                resultType: CameraResultType.Uri
            });
            return image;
        } catch (error) {
            console.error('Error taking picture:', error);
            return null;
        }
    }

    async getCurrentLocation() {
        try {
            const coordinates = await Geolocation.getCurrentPosition();
            return coordinates;
        } catch (error) {
            console.error('Error getting location:', error);
            return null;
        }
    }

    async getDeviceInfo() {
        try {
            const info = await Device.getInfo();
            return info;
        } catch (error) {
            console.error('Error getting device info:', error);
            return null;
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.tastackCapacitor = new TastackCapacitor();
});

// Export for use in other modules
export default TastackCapacitor;
