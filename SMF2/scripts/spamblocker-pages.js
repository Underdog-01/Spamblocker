/*
 * Pagination javascript file for Spamblocker 
 * Spam Blocker was developed for SMF forums c/o Underdog @ http://webdevelop.comli.com	
 * Copyright 2013 underdog@webdevelop.comli.com
 * This software package is distributed under the terms of its Freeware License
 * http://webdevelop.comli.com/index.php/page=spamblocker_license
*/
function Pager(tableName, itemsPerPage) {
    this.tableName = tableName;
    this.itemsPerPage = itemsPerPage;
    this.currentPage = 1;
    this.pages = 0;
    this.inited = false;
	var sbround = 0;
	
    this.showRecords = function(from, to) {        
        var rows = document.getElementById(tableName).rows;
        // i starts from 1 to skip first row in table
        for (var i = 1; i < rows.length; i++) {
            if (i < from || i > to)  
                rows[i].style.display = 'none';
            else
                rows[i].style.display = '';
        }
    }
    
    this.showPage = function(pageNumber) {
    	if (! this.inited) {
    		alert("not initiated");
    		return;
    	}

        var oldPageAnchor = document.getElementById('pg'+this.currentPage);
        oldPageAnchor.className = 'smalltext pg-normal';		
        
        this.currentPage = pageNumber;
        var newPageAnchor = document.getElementById('pg'+this.currentPage);
        newPageAnchor.className = 'largetext pg-selected';		
        
        var from = (pageNumber - 1) * itemsPerPage + 1;
        var to = from + itemsPerPage - 1;
        this.showRecords(from, to);
		
		document.getElementById("SBchangePage").innerHTML = spamblocker_page + ' ' + this.currentPage + ' ' + spamblocker_of + ' ' + this.pages;


    }   
    
    this.prev = function() {
        if (this.currentPage > 1)
            this.showPage(this.currentPage - 1);
    }
    
    this.next = function() {
        if (this.currentPage < this.pages) {
            this.showPage(this.currentPage + 1);
        }
	}	
	
    this.prev_plus = function() {
		if ((this.currentPage > Number(Xincrement)) && (this.currentPage < Number(Xincrement) * 2) && (Number(Xincrement) <= this.pages))
			this.showPage(Number(Xincrement));		
		else if (this.currentPage > Number(Xincrement))   
		{			
			if ((this.currentPage / Number(Xincrement)) % 1 != 0)
				sbround = ((Math.round(this.currentPage / Number(Xincrement)))) * Number(Xincrement) - Number(Xincrement);			
			else
				sbround = this.currentPage - Number(Xincrement);
			
			this.showPage(sbround);	
		}
		else
			this.showPage(1);		
    }
    
    this.next_plus = function() {		
		if ((this.currentPage > Number(Xincrement)) && (this.currentPage < Number(Xincrement) * 2) && (Number(Xincrement) * 2) <= this.pages)
			this.showPage(Number(Xincrement) * 2);
        else if (this.currentPage < this.pages - Number(Xincrement)) 
		{			
			if ((this.currentPage / Number(Xincrement)) % 1 != 0)
				sbround = ((Math.round(this.currentPage / Number(Xincrement)))) * Number(Xincrement) + Number(Xincrement);					
			else
				sbround = this.currentPage + Number(Xincrement);
				
			this.showPage(sbround);   
		}		
		else
			this.showPage(this.pages); 
    }                        
    
    this.init = function() {
        var rows = document.getElementById(tableName).rows;
        var records = (rows.length - 1); 
        this.pages = Math.ceil(records / itemsPerPage);
        this.inited = true;
    }

    this.showPageNav = function(pagerName, positionId) {
    	if (! this.inited) {
    		alert("not initiated");
    		return;
    	}
    	var element = document.getElementById(positionId); 
		var pagerHtml = '<span onclick="' + pagerName + '.prev_plus();" class="smalltext pg-normal"> ' + spamblocker_prev_plus + ' </span> ' + vertical_bar_y + ' ';
    	pagerHtml += '<span onclick="' + pagerName + '.prev();" class="smalltext pg-normal"> ' + spamblocker_prev + ' </span> ' + vertical_bar_y + ' ';
		for (var page = 1; page <= this.pages; page++) 
			pagerHtml += '<span id="pg' + page + '" class="smalltext pg-normal"></span>';		
		pagerHtml += '<span id="SBchangePage" style="position:relative;top:-3px;">' + spamblocker_page + ' ' + this.currentPage + ' ' + spamblocker_of + ' ' + this.pages + '</span>';	
		pagerHtml += '<span onclick="'+pagerName+'.next();" class="smalltext pg-normal"> ' + vertical_bar_y + ' ' + spamblocker_next + '</span>';  
		pagerHtml += ' ' + vertical_bar_y + ' <span onclick="'+pagerName+'.next_plus();" class="smalltext pg-normal">' + spamblocker_next_plus + '</span>'; 		
        element.innerHTML = pagerHtml;
    }
}
function regenerate()
{
	window.location.reload()
}

function regenerate2()
{
	if (document.layers)
	{
		appear()
		setTimeout("window.onresize=regenerate",450)
	}
}

function changetext(whichcontent)
{
	if (document.all||document.getElementById)
	{
		cross_el=document.getElementById? document.getElementById("descriptions"):document.all.descriptions
		cross_el.innerHTML='<div style="font-family:Arial Narrow Bold;font-size:small;">'+whichcontent+'</div>'
	}
	else if (document.layers)
	{
		document.d1.document.d2.document.write('<div style="font-family:Arial Narrow Bold;font-size:small;">'+whichcontent+'</div>')
		document.d1.document.d2.document.close()
	}

}

function appear()
{
	document.d1.visibility='show'
}
