(function(){
    document.getElementById("find").addEventListener("submit", function(e){
        e.preventDefault();
        inputs = {
            interest: getID("interest").value,
            gender: getID("gender").value,
            age: {
                min: getID("ageMin").value,
                max: getID("ageMax").value,
            },
            fame: {
                min: getID("fameMin").value,
                max: getID("fameMax").value,
            },
            city: getID("city").value,
        }

        search(inputs);
    });
})();

function search(inputs){
    $.post(`searchRes.php`, inputs, function(data, status){
        if (status === 'success'){
            try {
                let response = JSON.parse(data);

                if (response){
                    $("#searchResults").html('');
                    if (response.length > 0){
                        response.forEach(function(value, key) {
                            $("#searchResults").append(`
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="card" style="width: 100%; margin-top: 10px;">
                                        <img class="card-img-top" src="${value.ImageName}" alt="Card image cap">
                                        <div class="card-body">
                                            <h5 class="card-title"><a href="home.php?user=${value.UserID}">${value.Username}</a></h5>
                                            <p class="card-text">
                                                Fame Rating: ${value.Fame}
                                            </p>
                                        </div>
                                    </div> 
                                </div>
                            `);
                        });
                    }else{
                        $("#searchResults").html(`
                            <div class="alert alert-warning">No Match For You, Still lonely</div>
                        `);
                    }
                }
                
                
            } catch (error) {
                console.error(error);
            }
        }
        else{
            //console.log("something");
        }
    });
}

function getID(string){
    return document.getElementById(string);
}