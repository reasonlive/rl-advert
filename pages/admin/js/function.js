function setCookie (name, value, path, domain, secure, expires)
{
    document.cookie= name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires.toGMTString() : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}

function getCookie (name)
{
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1)
    {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    }
    else
    {
        begin += 2;
    }
    var end = document.cookie.indexOf(";", begin);
    if (end == -1)
    {
        end = dc.length;
    }
    return unescape(dc.substring(begin + prefix.length, end));
}

function deleteCookie (name, path, domain)
{
    if (getCookie(name))
    {
        document.cookie = name + "=" + 
            ((path) ? "; path=" + path : "") +
            ((domain) ? "; domain=" + domain : "") +
            "; expires=Thu, 01-Jan-70 00:00:01 GMT";
    }
}


function addLoadEvent (func)
{    
    var oldonload = window.onload;
    if (typeof window.onload != 'function')
    {
        window.onload = func;
    } 
    else 
    {
        window.onload = function()
        {
            oldonload();
            func();
        }
    }
}


function menu_init ()
{
	var menu = document.getElementById('nav');
	var subs = menu.childNodes;
	
	var j = 0;
	
	for (var i=0 ; subs[i]; i++)
	{
		if (subs[i].tagName=='LI')
		{
			hs = subs[i].getElementsByTagName('B');
			heading = hs[0];
			ss = subs[i].getElementsByTagName('UL');
			submenu = ss[0];
			
			j++;
			
			heading.onclick = function () { menu_toggle(this); };

			if (getCookie('menu'+j)=='1')
				 submenu.style.display = 'block';
			else if (getCookie('menu'+j)=='0')
				submenu.style.display = 'none';
			else if (j==1)
				submenu.style.display = 'block';
			else
				submenu.style.display = 'none';
		}
	}
}

function menu_toggle (heading)
{
	var section = heading.parentNode;
	var submenus = section.getElementsByTagName('UL');
	var submenu = submenus[0];
		
	if (submenu.style.display=='none')
		submenu.style.display = 'block';
	else
		submenu.style.display = 'none';
		
	var j = 0;

	var menu = document.getElementById('nav');
	var subs = menu.childNodes;
	for (var i=0 ; subs[i]; i++)
	{
		if (subs[i].tagName=='LI')
		{
			hs = subs[i].getElementsByTagName('B');
			h = hs[0];			
			j++;
			
			if (h==heading && submenu.style.display=='none')
				setCookie('menu'+j, '0', '/');
			else if (h==heading)
				setCookie('menu'+j, '1', '/');
		}
	}
		

}

addLoadEvent(menu_init);