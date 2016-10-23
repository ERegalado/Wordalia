(function() {
  var app = angular.module('wordalia', []);
  app.controller('WDController', function($http){
	var wdVault = this;
	wdVault.wd = []
    // $http.get('http://localhost/wordalia/be/index.php/wordalia/word') //LOcal
    $http.get(window.location.href+'be/index.php/wordalia/word') //Azure
		.success(function(data) {wdVault.wd = data.word;});
  });
  
})();
