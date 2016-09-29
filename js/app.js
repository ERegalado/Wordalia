(function() {
  var app = angular.module('wordalia', []);
  app.controller('WDController', function($http){
	var wdVault = this;
	wdVault.wd = []
	wdVault.hello = '';
    $http.get('http://localhost/wordalia/be/index.php/wordalia/word')
		.success(function(data) {
			wdVault.wd = data.word;
			wdVault.hello = 'Hola';
			console.log(wdVault.wd);
			console.log('head' + wdVault.head);
        });
		console.log('1 '+wdVault.wd);
  });
  
})();
