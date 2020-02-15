<script src="https://www.gstatic.com/firebasejs/5.5.8/firebase.js"></script>
<script>
    var auth_id = "{{  $auth->id }}";
    var auth_clinic = "{{ $auth->clinic_id }}";
    var role = "{{ $auth->role_id }}";

    // get the browser data
    function get_browser_info() {
        var ua = navigator.userAgent, tem,
            M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
        if (/trident/i.test(M[1])) {
            tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
            return {name: 'IE ', version: (tem[1] || '')};
        }
        if (M[1] === 'Chrome') {
            tem = ua.match(/\bOPR\/(\d+)/);
            if (tem != null) {
                return {name: 'Opera', version: tem[1]};
            }
        }
        M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
        if ((tem = ua.match(/version\/(\d+)/i)) != null) {
            M.splice(1, 1, tem[1]);
        }
        return {
            name: M[0],
            version: M[1]
        };
    }

    var browser = get_browser_info();
    var browser_name = browser.name;
    // console.log(browser.name);

    // Initialize Firebase        - get from firebase application page
    var config = {
        apiKey: "AIzaSyDMugJB-DC7NP4cD8-6F8tDQkQV9lKT6uc",
        authDomain: "rklinic-757dc.firebaseapp.com",
        databaseURL: "https://rklinic-757dc.firebaseio.com",
        projectId: "rklinic-757dc",
        storageBucket: "rklinic-757dc.appspot.com",
        messagingSenderId: "90317623054"
    };

    firebase.initializeApp(config);

    // Retrieve Firebase Messaging object.
    const messaging = firebase.messaging();
    messaging.requestPermission()
        .then(function () {
            // console.log('Notification permission granted.');
            return messaging.getToken();
        })
        .then(function (notification_token) {
            // console.log(notification_token);
            $.ajax({
                url: URL + '/notification/set-token',
                type: 'POST',
                data: {_token: token, notification_token: notification_token, browser: browser_name}
            }).done(function (data) {
            });
        })
        .catch(function (err) {
            // in case we could not set the Token
            console.log('Unable to get permission to notify.', err);
        });


    messaging.onMessage(function (payload) {

        // load notification indicator
        // increase the notification counter
        $('.notification-counter-click').load(URL + '/notifications/counter');

        const notification = JSON.parse(payload.data.notification);

        if (notification.multicast == 0) {
            // if receiver were only one
            if (auth_id == notification.receiver_id) {
                // authenticate notification
                // your code here
                iziToast.show({
                    theme: 'dark',
                    icon: 'icon-person',
                    image: "{{ asset('assets/images/logo/logo-height-65.png') }}",
                    imageWidth: 100,
                    title: payload.notification.title,
                    message: '<p>' + payload.notification.body + '</p>',
                    position: 'bottomRight', // bottomRight, bottomLeft, topRight, topLeft, topCenter, bottomCenter
                    progressBarColor: 'rgb(45, 48, 51)',
                    onOpening: function (instance, toast) {
                        console.info('callback abriu!');
                    },
                    onClosing: function (instance, toast, closedBy) {
                        console.info('closedBy: ' + closedBy); // tells if it was closed by 'drag' or 'button'
                    }
                });

                // load the part of notification list to reload the notifications number and notification list
                $('#notifications-list').load(URL + '/notifications/list');
                $('.notification-counter').load(URL + '/notifications/counter');
            }


        } else {
            // notifications emitted to every one that has role
            if (notification.multicast == role) {

                if (notification.receiver_id == auth_clinic || notification.receiver_id == auth_id) {
                    // notification to assistant in specific clinic
                    // your code here
                    iziToast.show({
                        theme: 'dark',
                        icon: 'icon-person',
                        image: "{{ asset('assets/images/logo/logo-height-65.png') }}",
                        imageWidth: 70,
                        timeout: 5000,
                        title: '<strong>' + payload.notification.title + '</strong>',
                        message: '<p>' + payload.notification.body + '</p>',
                        position: 'bottomRight', // bottomRight, bottomLeft, topRight, topLeft, topCenter, bottomCenter
                        progressBarColor: 'rgb(45, 48, 51)',

                        onOpening: function (instance, toast) {
                            console.info('callback abriu!');
                        },
                        onClosing: function (instance, toast, closedBy) {
                            console.info('closedBy: ' + closedBy); // tells if it was closed by 'drag' or 'button'
                        }
                    });

                    // load the part of notification list to reload the notifications number and notification list
                    $('#notifications-list').load(URL + '/notifications/list');
                    $('.notification-counter').load(URL + '/notifications/counter');
                }
            }
        }

        $('.iziToast').on("click", function () {
            window.location.replace(notification.url)
        });
    });
</script>
