import firebase from 'firebase';
import {bus} from '../app';
export default{
  methods:{
    goRoute(route){
      this.$router.push(route).catch(err => {});
    },
    googleLogin(){
      var provider = new firebase.auth.GoogleAuthProvider();
      var _this = this;
      firebase.auth().signInWithPopup(provider).then(function(result) {
        firebase.auth().currentUser.getIdToken(false).then(function(token) {
          _this.$http.post(_this.$root.api + '/users', {
            name  : result.user.displayName,
            email : result.user.email,
            image : result.user.photoURL,
            uid   : result.user.uid
          }).then((response)=>{
            if(response.body.success){
              console.log(response.body);
              response.body.data[0].token = token;
              _this.$store.dispatch('setUser',response.body.data[0]);
              _this.$store.dispatch('toggle_Login',true);
              bus.$emit('close_login');
            }
          })
          .then((error)=>{
            console.log(error);
          })
        }).catch(function(error) {
          console.log(error);
        });



      }).catch(function(error) {
        // Handle Errors here.
        var errorCode = error.code;
        var errorMessage = error.message;
        // The email of the user's account used.
        var email = error.email;
        // The firebase.auth.AuthCredential type that was used.
        var credential = error.credential;
        // ...
      });

    },
    facebooklogin(){
      console.log('logged');
      var provider = new firebase.auth.FacebookAuthProvider();
      firebase.auth().signInWithPopup(provider).then(function(result) {
        firebase.auth().currentUser.getIdToken(false).then(function(token) {
          _this.$http.post(_this.$root.api + '/users', {
            name  : result.user.displayName,
            email : result.user.email,
            image : result.user.photoURL,
            uid   : result.user.uid
          }).then((response)=>{
            if(response.body.success){
              console.log(response.body);
              response.body.data[0].token = token;
              _this.$store.dispatch('setUser',response.body.data[0]);
              _this.$store.dispatch('toggle_Login',true);
              bus.$emit('close_login');
            }
          })
          .then((error)=>{
            console.log(error);
          })
        }).catch(function(error) {
          console.log(error);
        });
      }).catch(function(error) {
        // Handle Errors here.
        console.log(error);
        var errorCode = error.code;
        var errorMessage = error.message;
        // The email of the user's account used.
        var email = error.email;
        // The firebase.auth.AuthCredential type that was used.
        var credential = error.credential;
        // ...
      });
  },
    checklogin(){
      var _this = this;
      firebase.auth().onAuthStateChanged((user)=>{
        if (user) {
          return true;
        } else {
          return false;
        }
      });
    },
    logout(){
      firebase.auth().signOut().then(()=>{
        this.$store.dispatch('setUser', {
          name  : null,
          email : null,
          image : null
        });
        this.$store.dispatch('toggle_Login',false);
        this.goRoute('/');
      })
    },
    getNotification(user_id){
      this.$http.get(this.$root.api + '/notifications/' + this.$store.getters.getUser.id, {
        headers: {
            Authorization: 'Bearer '+ this.$store.getters.getUser.token
        }
      }).then((response)=>{
        console.log(response.body);
        this.$store.dispatch('setNoti',response.body.data);
        this.$store.dispatch('setNotiCount',response.body.meta.total);
      })
    },
    async getNotiToken(subscribe){
      try {
        const messaging = firebase.messaging();
        await messaging.requestPermission();
        const token = await messaging.getToken();
        // console.log('I got the token :', token);
        var _this =this;
        this.$http.post(_this.$root.api + '/notificationtokens/', {
          user_id   : _this.$store.getters.getUser.id,
          token     : token,
          subscribe : subscribe
        },
        {
          headers: {
              Authorization: 'Bearer '+ this.$store.getters.getUser.token
          }
        }).then((response)=>{
          console.log(response);
        })
        .then((error)=>{
          console.log(error);
        })
      } catch (error) {
        console.error(error);
      }
   },
  }
}
