/***
* Paginator Object
* Author: Andrew Chapman of SIMB
* 
* @param form The form object
* @param selector_append_to  A jQuery selector to append the pagination to, e.g. "div.main"
*/
var Paginator = function(form, selector_append_to)
{
    var self = this;
    this.form = form;
    this.items_per_page = this.form.find("#items_per_page").val();
    this.selector_total_recs = this.form.find("#count_all");    
    this.selector_append_to = $(selector_append_to);
    this.current_page = this.form.find("#current_page").val();
    this.num_pages = 0;
    this.paging_changed = false;
    
    /***
    * Refreshes the pagination.
    * Call this after the listing has been loaded or reloaded.
    */
    this.refresh = function()
    {
        // Remove any existing pagination.
        this.removePagination(); 

        // Figure out how many pages there are
        var total_recs = this.selector_total_recs.val();
        if(isNaN(total_recs))
        {
            return; 
        }
        
        total_recs = parseInt(total_recs);
        this.num_pages = Math.floor(total_recs / this.items_per_page) + 1;
        
        if(this.num_pages <= 1)
        {
            this.removePagination();
            return;    
        }
        
        // Render the HTML for the pagination.
        var html = '<ul class="pagination">';
        
        if(this.num_pages > 1)
        {
            html += '<li><a href="#" class="prev">&laquo; Prev</a></li>';     
        }
        
        for(p = 1; p <= this.num_pages; p++)
        {
            html += '<li><a href="' + p + '" class="page';
            
            if(p == this.current_page)
            {
                html += ' active';    
            }
            
            html += '">' + p + '</a></li>';    
        }
        
        if(this.num_pages > 1)
        {
            html += '<li><a href="#" class="next">Next &raquo;</a></li>';
        }        
        
        html += '</ul>';
        
        // Append the HTML to the selector
        $(this.selector_append_to).append(html);
        
        // Find pagination events
        this.bindEvents();       
        
        // Reset the paging_changed flag
        this.paging_changed = false;
    }
    
    /***
    * Removes the pagination from the document.
    */
    this.removePagination = function()
    {
        this.unbindEvents();
        $(this.selector_append_to).find("ul.pagination").remove();    
    }
    
    /***
    * Removes any event bindings for the pagination.
    */
    this.unbindEvents = function()
    {
        $(this.selector_append_to).find("ul.pagination a").unbind();        
    }
    
    /***
    * Binds click events on the pagination.
    */
    this.bindEvents = function()
    {
        // Handle the event when the user clicks on the PREV button
        $(this.selector_append_to).find("ul.pagination a.prev").click(function(e)
        {
            e.preventDefault();
            
            if(self.current_page <= 1)
            {
                return;
            }
            
            self.loadPage(parseInt(self.current_page) - 1);
        }); 
        
        // Handle the event when the user clicks on the NEXT button
        $(this.selector_append_to).find("ul.pagination a.next").click(function(e)
        {
            e.preventDefault();
            
            if(self.current_page >= self.num_pages)
            {
                return;
            }
            
            self.loadPage(parseInt(self.current_page) + 1);
        });   
        
        // Handle the event when the user clicks on a page number
        $(this.selector_append_to).find("ul.pagination a.page").click(function(e)
        {
            e.preventDefault();
            
            // Get the page number that was clicked upon
            var page = parseInt($(this).attr("href"));
            
            if(page == self.current_page)
            {
                return;    
            }
            
            self.loadPage(page);
        });                      
    }
    
    /***
    * Reload the listing that this pagination is tied too.
    */
    this.loadPage = function(page)
    {
        self.current_page = page;
        self.paging_changed = true;
        
        // Set the current page in the form and in the list class
        self.form.find("#current_page").val(page);
        
        $(self.selector_append_to).find("ul.pagination a").removeClass("active");
        $(this).addClass("active");
        
        // Submit the form
        self.form.submit();        
    }
}