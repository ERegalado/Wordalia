(function() {
  var app = angular.module('wordalia', []);
  app.controller('WDController', function($http){
	var wdVault = this;
	wdVault.wd = []
	wdVault.hello = '';
    $http.get('http://localhost/wordalia/index.php/Wordalia/word')
		.success(function(data) {
            wdVault.wd = data.word[0];
			wdVault.hello = 'Hola';
			console.log(wdVault.wd);
        });
		console.log('1 '+wdVault.wd);
  });
  
})();
