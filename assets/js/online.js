window.addEventListener('load', function(){
	var getUsersConnected, userConnected, functions;

	getUsersConnected = function(){
		$.post(window.miniSite.baseURL, {action: 'GetUsersConnected', requestType: 'ajax'},
			function(res){
				if(res.users) {
					var rows = '<tr>\
					<td>User Name</td>\
				<td>Last Login</td>\
				<td>Last Update</td>\
				<td>IP</td>\
				</tr>';
					for(var i in res.users) {
						var row = res.users[i];
						rows += '<tr> <td>' + row.username +
						'</td> <td>' + row.connected_date +
						'</td> <td>' + row.update_date +
						'</td> <td>' + row.IP +
						'</td> </tr>';
					}
					$('#users-connected').html(rows);
				}
				else {
					if(res.redirect) {
						window.location.reload();
					}
					else {
						$('#users-connected').html('');
					}
				}
			}, 'json'
		);
	};
	userConnected = function(){
		$.post(window.miniSite.baseURL, {action: 'UpdateUserConnected', requestType: 'ajax'});
	};
	functions = {
		getUsersConnected: {func: getUsersConnected, milliSeconds: 3000},
		userConnected: {func: userConnected, milliSeconds: window.miniSite.updateUserTime}
	};
	for(var i in functions) {
		setInterval(functions[i].func, functions[i].milliSeconds);
	}
});