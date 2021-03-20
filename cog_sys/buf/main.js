'use strict';
{
    function update(){
    document.querySelector('button').addEventListener('click',()=>{
        

        document.getElementById('target1').className = 'role1';
        document.getElementById('target2').className = 'role1';
        document.getElementById('target3').className = 'role1';

    });
        }
        update();
}