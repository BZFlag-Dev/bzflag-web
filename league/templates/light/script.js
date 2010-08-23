var defOff= '#fafabb';
var defOn = '#e4e488';
var clrOff = '#aafaaa';
var clrOn  = '#66dd66';
var admOff = '#ffb800';
var admOn  = '#dd8800';

function butRoll (obj, theme, onoff){
	if (onoff){
		if (theme=='clrbut')
			obj.style.backgroundColor = clrOn;
		else if (theme=='admbut')
			obj.style.backgroundColor = admOn;
		else
			obj.style.backgroundColor = defOn;
	}else{
		if (theme=='clrbut')
			obj.style.backgroundColor = clrOff;
		else if (theme=='admbut')
			obj.style.backgroundColor = admOff;
		else
			obj.style.backgroundColor = defOff;
	}
}

