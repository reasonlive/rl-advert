function hideserfaddblock(bname)
{
    if (document.getElementById(bname).style.display == 'none')
	{
        document.getElementById(bname).style.display = '';
    }
	else
	{
        document.getElementById(bname).style.display = 'none';
    }
    return false;
}

            function tack(x){ 
if (document.getElementById(x).style.display == 'none') { 
document.getElementById(x).style.display = ''; 
} else { document.getElementById(x).style.display = 'none'; } 
return false;  
}