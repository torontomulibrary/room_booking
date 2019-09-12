/******************************
Protected by Copyright
Â© Propeller Studios Inc.
on behalf of
Ryerson University
Faculty of Community Services
******************************/

/*****************************
TABLE OF CONTENTS.
CTRL + F the code to jump to that section faster
----
S1.0 - Breadcrumbs
S2.0 - Headline/toolbar full width
S3.0 - Slideshow
S4.0 - Newslist component
S5.0 - Newsdetail component
S6.0 - Biography list component
S7.0 - Biography component
S8.0 - Events list
S9.0 - Megamenu component
S10.0 - Social media section
S11.0 - Tabs component
S12.0 - Header component
S13.0 - Homepage toolbar
S14.0 - Event release component
----
----
//** Descriptions
S1.0 - Breadcrumbs
 - Creates a dynamic breadcrumbs component via the URL data

S2.0 - Headline/toolbar full width
 - Moves the toolbar content above the other main content.

S3.0 - Slideshow
 - Slick slideshow code for the slideshow on the home-page

S4.0 - Newslist component
 - Reorder the text on the news list component

S5.0 - Newsdetail component
 - Reorders the text on the news release component

S6.0 - Biography list component
 - Reorders text on the biography list component

S7.0 - Biography component
 - Reorders the text on the biography component. Also, moves items inside the accordion component
   and out of the biography component itself

S8.0 - Events list
 - Reorders the text on the events list component

S9.0 - Megamenu component
 - Remove the <a> link tags from the text in the first column in the mega menu

S10.0 - Social media section
 - Hides the text from the social media section so the background image can show

S11.0 - Tabs component
 - Adds the left and right panels for when a there's too many tabs on a page. Allows for
   maneuverability between the tabs. Hides when there aren't that many tabs, and automatically
   shows when the tab has too many items.

S12.0 - Header component
 - Extracts then wraps the two text on top of the page with divs to style them easier. The first text is small
   while the other text is set to large. Can change the styling in the CSS

S13.0 - Homepage toolbar
 - Fixes the toolbar on the home-page to fit the toolbar better since the toolbar
   works on the other pages just with CSS and not the homepage (since the toolbar is
   moved away from the page-specific class)

S14.0 - Event release component
 - Rorders the text on the even release component. Also, adds the "website" text prepending the events website link

******************************/
/*****************************
INLINE JAVASCRIPT

All the outer most container/columns component has a page div to name the page template
CODE: <script> jQuery(".mt_mainContent").children("div").children(".uiwColumns").eq(1).children(".columnsComponent").addClass("specific-page"); </script>
Location: Outer most container/columns on the page. In "JS after"

Homepage page
Each section has code to insert a wrapper inbetween sections. Used to style the areas easier
Location:
    Located on the outer most div on the sections
    >> CODE [Before JS] : <script>document.write('<div class="section-area">');</script>
    >> CODE [After JS]: <script>document.write('</div>');</script>
     - Headline
     - Feature
     - Our programs
     - Recent News
     - Upcoming Events
     - What We're Tweeting

     !!! WARNING !!!
      ADDING - If you want to add a section using this method, you !!MUST!! Add the AFTER JS code first or else
               you might experience severe complications with the page structure.
      REMOVING - When removing the section, remove the BEFORE JS code FIRST then the AFTER JS after. This will lead to less complications
                 with the code that's located inside the BEFORE JS section.
     !!! CAUTION !!!
        - If you're looking to add the .glance-area div, you NEED to follow the warnings above.
        - If you wish to remove the section, you must remove the outer coulmns component out to get rid of any trace of the
          <script>document.write('<div class="section-area">');</script> code

Homepage page
 What We're Tweeting area
    Location: Text component in the "What We're Tweeting" area and in BEFORE JS
    Code: <script>
            ! function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0],
                    p = /^http:/.test(d.location) ? 'http' : 'https';
                if (!d.getElementById(id)) {
                    js = d.createElement(s);
                    js.id = id;
                    js.src = p + "://platform.twitter.com/widgets.js";
                    fjs.parentNode.insertBefore(js, fjs);
                }
            }(document, "script", "twitter-wjs");
        </script>
    Usage: Making the Twitter area functionable

Program details page
    "Program at a glance" outer columns/container has code to make it look that way
    Location: Outer most solumns/container on the "Programs at a glance" box
    >> CODE [Before JS]: <script>document.write('<div class="glance-area">');</script>
    >> CODE [After JS]: <script>document.write('</div>');</script>
    !!! WARNING !!!
      ADDING - If you want to add a section using this method, you !!MUST!! Add the AFTER JS code first or else
               you might experience severe complications with the page structure.
      REMOVING - When removing the section, remove the BEFORE JS code FIRST then the AFTER JS after. This will lead to less complications
                 with the code that's located inside the BEFORE JS section.
     !!! CAUTION !!!
        - If you're looking to add the .glance-area div, you NEED to follow the warnings above.
        - If you wish to remove the section, you must remove the outer coulmns component out to get rid of any trace of the
          <script>document.write('<div class="glance-area">');</script> code

*****************************/

var $jqCustom2 = jQuery.noConflict();

$jqCustom2(document).ready(function($) {
    var debugMode = 0; //CHANGE TO 1 FOR DEBUG MODE

    //******** S1.0 - Start of breadcrumbs code ********//
    
    var parts = '',
        output = '',
        here = location.href.split('/').slice(3);

    function titleCase(str) {
        var newText = str.split(" ");
        //console.log(newText);
        for (var i = 0; i < newText.length; i++) {
            var temp = newText[i].substring(1).toLowerCase();
            newText[i] = newText[i].charAt(0).toUpperCase() + temp;
        }
        newText = newText.join(" ");
        return newText;
    }

    for (var i = 0; i < here.length; i++) {

        var text = here[i].replace(/-/g, " ");
        text = text.replace(/\band\b/g, "&");
        text = titleCase(text);


        var link = '/' + here.slice(0, i + 1).join('/');

        if (i == 0) {
            text = 'Home';
        }

        // check if the url ends with index.html
        if (document.URL.indexOf("index.html") >= 0) {
            if (i < here.length - 2) {
                output += '<a href="' + link + '">' + text + '</a>  > ';
            } else if (i < here.length - 1) {
                output += '<b>' + text + '</b>';
            }
        } else {
            if (i < here.length - 1) {
                output += '<a href="' + link + '">' + text + '</a>  > ';
            } else {
                output += '<b>' + text.substring(0, text.indexOf('.')) + '</b>';
            }
        }
    }
    $('#breadcrumbs').html(output);

    //******** End of breadcrumbs code ********//
    //******** S2.0 - Start of headline full width code ********//
    $(".toolbar-area")
        .closest(".uiwColumns")
        .addClass("page-banner")
        .insertBefore(".contentArea");
    //******** End of headline full width code ********//
    //******** S3.0 SlideShow Code START********//

        $('.home-page .our-programs-area .tabsComponent .panes').slick({
            lazyLoad: 'ondemand'
        });
    //******** SlideShow Code END********//
    //******** S4.0 Start of Newslist code ********//

    var newsList =  document.querySelectorAll('.news-list-page .newsListComponent');

    //console.log(newsList);

    if(newsList != null){
        for(var j = 0; j < newsList.length; j++){
            var newsListList = newsList[j].querySelector("ul");
            var newsListItems = newsListList.querySelectorAll("li");
            var newsListLength = newsListItems.length;

            for(var i = 0; i < newsListLength; i++){

                newsListItems[i].querySelector('.newsTitle').parentNode.insertBefore(newsListItems[i].querySelector('.newsDate'), newsListItems[i].querySelector('.newsTitle').nextSibling);
                newsListItems[i].querySelector('.newsAuthor').parentNode.insertBefore(newsListItems[i].querySelector('.newsSummary'), newsListItems[i].querySelector('.newsDate').nextSibling);
                newsListItems[i].querySelector('.newsCategory').parentNode.insertBefore(newsListItems[i].querySelector('.newsCategory'), newsListItems[i].querySelector('.newsAuthor').nextSibling);
                newsListItems[i].querySelector('.newsSummary').parentNode.insertBefore(newsListItems[i].querySelector('.newsSummary'), newsListItems[i].querySelector('.newsCategory').nextSibling);
            }
        }
    }
    //******** End of Newslist code ********//
    //******** S5.0 - Start of Newsdetail code ********//

    var newsListRelease =  document.querySelector('.newsrelease');

    //console.log(newsListRelease);

    if(newsListRelease != null){

        var newsReleaseDate = newsListRelease.querySelector('.news_fullstory_dateline');
        var newsReleaseAuthor = newsListRelease.querySelector('.news_fullstory_author');
        var newsReleaseTitle = newsListRelease.querySelector('.newsrelease_title');
        var newsReleaseSubtitle = newsListRelease.querySelector('.newsrelease_subtitle');
        var newsReleaseCategory = newsListRelease.querySelector('.newsrelease_category');
        var newsReleaseImg = newsListRelease.querySelector('.image_cap_block');

        var newDiv = document.createElement('div');
        newDiv.className = 'news-release-details-area';

        newsReleaseTitle.parentNode.insertBefore(newsReleaseCategory, newsReleaseTitle.nextSibling);
        newsReleaseSubtitle.parentNode.insertBefore(newsReleaseCategory, newsReleaseSubtitle.nextSibling);
        newsReleaseDate.parentNode.insertBefore(newsReleaseAuthor, newsReleaseDate.nextSibling);
        newsReleaseAuthor.parentNode.insertBefore(newsReleaseCategory, newsReleaseAuthor.nextSibling);

        newsReleaseDate.parentNode.insertBefore(newDiv, newsReleaseDate);

        var newsReleaseDetailsBox = newsListRelease.querySelector('.news-release-details-area');

        newsReleaseDetailsBox.appendChild(newsReleaseDate);
        newsReleaseDetailsBox.appendChild(newsReleaseAuthor);
        newsReleaseDetailsBox.appendChild(newsReleaseCategory);

        newsReleaseImg.parentNode.insertBefore(newsReleaseDetailsBox, newsReleaseImg.nextSibling);
    }
    //******** End of Newsdetail code ********//
    //******** S6.0 - Start of Biography List code ********//

    var biographyList =  document.querySelectorAll('.biographyListComponent');


    if(biographyList != null){
        for(var i = 0; i < biographyList.length; i++){
            var biographyListItems = biographyList[i].querySelectorAll('ul.biographyList>li');
            var biographyListItemsLength = biographyListItems.length;

            for(var j = 0; j < biographyListItemsLength; j++){
                /**
                 *  Append all fields to the biographyContentContainer
                 * Checks to see if the current field exists
                 */
                if(biographyListItems[j].querySelector('.biographyPosition')){
                    biographyListItems[j].querySelector('.biographyListTeaserContentContainer').appendChild(biographyListItems[j].querySelector('.biographyPosition'));
                }
                if(biographyListItems[j].querySelector('.biographyTelephone')){
                    biographyListItems[j].querySelector('.biographyListTeaserContentContainer').appendChild(biographyListItems[j].querySelector('.biographyTelephone'));
                }
                if(biographyListItems[j].querySelector('.biographyEmail')){
                    biographyListItems[j].querySelector('.biographyListTeaserContentContainer').appendChild(biographyListItems[j].querySelector('.biographyEmail'));
                }
                if(biographyListItems[j].querySelector('.biographySpecialization')){
                    biographyListItems[j].querySelector('.biographyListTeaserContentContainer').appendChild(biographyListItems[j].querySelector('.biographySpecialization'));
                }
                if(biographyListItems[j].querySelector('.biographyWebsite')){
                    biographyListItems[j].querySelector('.biographyListTeaserContentContainer').appendChild(biographyListItems[j].querySelector('.biographyWebsite'));
                }
            }
        }
    }
    //******** End of biography listcode ********//
    //******** S7.0 Start of Biography code ********//

    var biography =  document.querySelector('.biography');

    if(biography != null){
        var biographyTeaser = biography.querySelector('#bioTeaser');
        var biographyImage = biography.querySelector('.image');
        var biographyName = biography.querySelector('.text');
        var biographyBiography = biography.querySelector('#biography');
        var biographyAffiliations = biography.querySelector('#professionalaffiliations');
        var biographyDepartment = biography.querySelector('#department');
        var biographyResearchInterests = biography.querySelector('#researchinterest');
        var biographyCurrentCourses = biography.querySelector('#currentcourses');
        var biographyPublicationsAndPresentations = biography.querySelector('#fullpubpreslist');
        var biographyPosition = biography.querySelector('#positioncurrentlyheld');
        var biographyTitle = biography.querySelector('#title');
        var biographyQualifications = biography.querySelector('#qualifications');
        var biographyCategory = biography.querySelector('#category');
        var biographyDoctorate = biography.querySelector('#doctorate');
        var biographyAddress = biography.querySelector('#address');
        var biographyEmail = biography.querySelector('#email');
        var biographyTelephone = biography.querySelector('#telephone');
        var biographyCellphone = biography.querySelector('#cellphone');
        var biographyWebsite = biography.querySelector('#website');
        var biographySpecialization = biography.querySelector('#specialization');
        var biographySelectedPublications = biography.querySelector('#selectedpublications');
        var biographyLanguages = biography.querySelector('#spokenlanguages');
        var biographyOther = biography.querySelector('#other');
        var biographyIntegration = biography.querySelector('#integration');
        var biographyResearchInterest = biography.querySelector('#researchinterest');
        var biographyCurrentCourses = biography.querySelector('#currentcourses');
        var biographyFullPubList = biography.querySelector('#fullpubpreslist');
        var biographyProfessionalAffiliations = biography.querySelector('#professionalaffiliations');

        if(biographyPosition){
            biographyPosition.querySelector('h5').innerHTML = 'Position';
        }
        if(biographyTitle){
            console.log('found title...');
            biographyTitle.querySelector('h5').innerHTML = 'Designation';
        }
        if(biographySpecialization){
            biographySpecialization.querySelector('h5').innerHTML = 'Areas of expertise';
        }
        if(biographyWebsite){
            biographyWebsite.querySelector('h5').innerHTML = 'Website';
        }

        //console.log(biographyTeaser);
        if(biographyPosition){
            biographyTeaser.appendChild(biographyPosition);
        }
        if(biographyTitle){
            biographyTeaser.appendChild(biographyTitle);
        }
        if(biographySpecialization){
            biographyTeaser.appendChild(biographySpecialization);
        }
        if(biographyWebsite){
            biographyTeaser.appendChild(biographyWebsite);
        }

        if(!biographyPosition && !biographyTitle && !biographySpecialization && !biographyWebsite){
            biographyTeaser.style.display = 'none';
        }

        biographyTeaser.parentNode.insertBefore(biographyImage, biographyTeaser.parentNode.firstChild);
        if(biographyPosition && biographyTitle){
            biographyPosition.parentNode.insertBefore(biographyTitle, biographyPosition.nextSibling);
        }
        if(biographyTitle && biographySpecialization){
            biographyTitle.parentNode.insertBefore(biographySpecialization, biographyTitle.nextSibling);
        }
        if(biographyWebsite && biographyTitle){
            biographyTitle.parentNode.appendChild(biographyWebsite);
        }

        var accordion = document.querySelectorAll('.accordionComponent .accordion');

        //console.log(biographyName);
        //console.log(accordion);

        document.querySelector('#breadcrumbs').parentNode.appendChild(biographyName);
        //Generates the current list item link from the title
        if(biographyBiography){
            accordion[0].querySelector('.content').appendChild(biographyBiography); //Biography section
        }else{
            accordion[0].style.display = 'none';
        }
        if(biographyCurrentCourses){
            accordion[1].querySelector('.content').appendChild(biographyCurrentCourses); //Teaching section
        }else{
            accordion[1].style.display = 'none';
        }
        if(biographyResearchInterests){
            accordion[2].querySelector('.content').appendChild(biographyResearchInterests); //Research section
        }else{
            accordion[2].style.display = 'none';
        }
        if(biographyPublicationsAndPresentations){
            accordion[3].querySelector('.content').appendChild(biographyPublicationsAndPresentations); // Creative Activity Section
        }else{
            accordion[3].style.display = 'none';
        }
        if(biographySelectedPublications){
            accordion[4].querySelector('.content').appendChild(biographySelectedPublications); //Publication Section
        }else{
            accordion[4].style.display = 'none';
        }
        if(biographyAffiliations){
            accordion[5].querySelector('.content').appendChild(biographyAffiliations); //Awards and Honours Section
        }else{
            accordion[5].style.display = 'none';
        }
    }
    //******** End of biography code ********//
    //******** S8.0 - Start of Events List code ********//

    var eventsList =  document.querySelectorAll('.eventsListComponent');

    if(eventsList != null){
        for(var j = 0; j < eventsList.length; j++){
            var eventsListItems = eventsList[j].querySelectorAll('li');
            var eventsListLength = eventsListItems.length;

            //console.log(eventsListItems);
            //console.log(eventsListLength);

            for(var i = 0; i < eventsListLength; i++){
                //Generates the current list item link from the title
                //console.log("in the eventslist loop");
                if(eventsListItems[i].querySelector('.eventDate')){
                    eventsListItems[i].querySelector('.eventTitle').parentNode.insertBefore(eventsListItems[i].querySelector('.eventDate'), eventsListItems[i].querySelector('.eventTitle').nextSibling);
                }
            }
        }
    }
    //******** End of Events List code ********//
    //******** S9.0 - Start of Header code ********//

    var headerLinks =  document.querySelector('#megaMenuContainer');

    if(headerLinks != null){
        var headerLinksLinks = headerLinks.querySelectorAll('.links');
        for(var j = 0; j < headerLinksLinks.length; j++){
            var headerSubLinks = headerLinksLinks[j].querySelectorAll('.subLinks');

            var columnHeaderTextTemp = headerSubLinks[0].querySelector('.columnHeader a').innerHTML;
            var columnBlurbTextTemp = headerSubLinks[0].querySelectorAll('a')[1].innerHTML;
            var columnBlurbArea = headerSubLinks[0].querySelectorAll('a')[1];
            headerSubLinks[0].querySelector('.columnHeader').innerHTML = '<p class="blurb-header">' + columnHeaderTextTemp + '</p>';
            columnBlurbArea.outerHTML = '<p class="blurb-text-area">' + columnBlurbTextTemp + '</p>';
        }
    }
    //******** End of Header code ********//
    //******** S10.0 - Start of social media code ********//

    var socialMedia =  document.querySelectorAll('.social-media-area');

    if(socialMedia != null){
        for(var i = 0; i < socialMedia.length; i++){
            var smFacebook = socialMedia[i].querySelector('.facebook');
            var smTwitter = socialMedia[i].querySelector('.twitter');
            var smYoutube = socialMedia[i].querySelector('.youtube');
            var smInstagram = socialMedia[i].querySelector('.instagram');


            if(smFacebook){
                smFacebook.innerHTML = "";
            }
            if(smTwitter){
                smTwitter.innerHTML = "";
            }
            if(smYoutube){
                smYoutube.innerHTML = "";
            }
            if(smInstagram){
                smInstagram.innerHTML = "";
            }
        }
    }
    //******** End of social media code ********//
    //******** S11.0 - Start of tabs code ********//
    var tabsComponent =  document.querySelector('.tabsComponent');

    if(tabsComponent  != null){
        var tabsTabs = tabsComponent.querySelector('.tabs');
        var tabsUl = tabsComponent.querySelector('ul');
        var tabsLi = tabsComponent.querySelectorAll('li');
        var tabsLiLength = tabsLi.length;
        var scrollArrowLeft = document.createElement('div');
        scrollArrowLeft.className = 'scroll-arrow__left';

        var leftArrow = document.createElement('div');
        leftArrow.className = 'arrow-left';
        leftArrow.innerHTML = '<';

        var scrollArrowRight = document.createElement('div');
        scrollArrowRight.className = 'scroll-arrow__right';

        var rightArrow = document.createElement('div');
        rightArrow.className = 'arrow-right';
        rightArrow.innerHTML = '>';

        var totalLiWidth = 0;

        var repeater = 0;
        var scrollLeft = 0;

        var scrollSpeed = 3;
        var repeaterIntervals = 8;

        scrollArrowRight.appendChild(rightArrow);
        scrollArrowLeft.appendChild(leftArrow);
        tabsUl.parentNode.insertBefore(scrollArrowLeft, tabsUl.nextSibling);
        tabsUl.parentNode.insertBefore(scrollArrowRight, tabsUl.nextSibling);

        for(var i = 0; i < tabsLiLength; i++){
            //console.log("tabsLi.offsetWidth " + tabsLi[i].offsetWidth);
            totalLiWidth += tabsLi[i].offsetWidth;
        }

        function scrollTabRight(){
            tabsComponent.querySelector('.tabs').scrollLeft += scrollSpeed;

            if(tabsTabs.scrollWidth <= tabsTabs.scrollLeft + tabsComponent.scrollWidth){
                clearInterval(repeater);
                scrollArrowLeft.style.display = "block";
                scrollArrowRight.style.display = "none";
            }else{
                scrollArrowLeft.style.display = "block";
                scrollArrowRight.style.display = "block";
            }
        }

        function scrollTabLeft(){
            tabsComponent.querySelector('.tabs').scrollLeft -= scrollSpeed;
            scrollLeft = (tabsComponent.querySelector('.tabs').pageXOffset !== undefined) ? tabsComponent.querySelector('.tabs').pageXOffset : tabsComponent.querySelector('.tabs').scrollLeft;
            if(scrollLeft <= 0){
                clearInterval(repeater);
                scrollArrowLeft.style.display = "none";
                scrollArrowRight.style.display = "block";
            }else{
                scrollArrowLeft.style.display = "block";
                scrollArrowRight.style.display = "block";
            }
        }

        if (totalLiWidth > tabsComponent.scrollWidth){
            scrollArrowRight.style.display = "block";
            scrollArrowLeft.style.display = "none";
            //tabsUl.style.overflowX = "auto";
            scrollArrowLeft.addEventListener('mouseout', function(){
                clearInterval(repeater);
            });
            scrollArrowLeft.addEventListener('mouseover', function(){
                repeater = setInterval(scrollTabLeft, repeaterIntervals);
            });
            scrollArrowRight.addEventListener('mouseout', function(){
                clearInterval(repeater);
            });
            scrollArrowRight.addEventListener('mouseover', function(){
                repeater = setInterval(scrollTabRight, repeaterIntervals);
            });
            /*
            tabsTabs.addEventListener('scroll', function(){
                if(tabsTabs.scrollWidth == tabsTabs.scrollLeft + tabsComponent.scrollWidth){
                    clearInterval(repeater);
                }
            });
            */
        }else{
            tabsUl.style.overflowX = "hidden";
            scrollArrowRight.style.display = "none";
            scrollArrowLeft.style.display = "none";
        }
    }
    //******** End of tabs code ********//
    //******** S12.0 - Start of header code ********//
    /*
    *   Description: Splits the main header text to two variables so it's easier to manipulate and add different styling to them
    *
    *   Tries to grab a header component
    */
    var headerComp = document.querySelector('.header');

    /*
    *   Check to see if the header is present or not
    */
    if(headerComp != null){
		// Check if Display Site Name is enabled
		if (jQuery(".header h1").length != 0) {
			/*
			*   Variable initialization
			*/
			var headerTitleInner = document.querySelector('.header').querySelector('h1').innerHTML; //Grabs the existing text that was inputted by the user
			var output = '';
			
			// Check if a Site Subtitle has been entered
			if (jQuery(".header h1 br").length != 0) {
				var splitTitle = headerTitleInner.split('<br>');

				//console.log(splitTitle);

				for(var i = 0; i < splitTitle.length; i++){
					if(splitTitle[i].toLowerCase().trim() === 'sample site name'.toLowerCase().trim()){
						output += '<div class="header-title-' + [i] + '"></div>'
					}else{
						output += '<div class="header-title-' + [i] + '">' + splitTitle[i].trim() + '</div>';
					}
					//console.log(splitTitle[i]);
				}
			} else {
				output += '<div class="header-title-1">' + headerTitleInner.trim() + '</div>'; // hardcode to header-title-1 class due to styling.
			}

			headerComp.querySelector('h1').innerHTML = output;
		}
    }
    //******** End of header code ********//
    //******** S13.0 - Start of homepage toolbar fix code ********//
    /*
    *   Check to see if the homepage is the current page
    *   This part is to fix the weird spacing regarding the toolbar element on the homepage
    */
    var homepagePage = document.querySelector('.home-page');

    /*
    *   Check to see if the homepage exists or is the current page
    */
    if(homepagePage != null){
        /*
        *   Styling fixes for the toolbar
        */
        document.querySelector('.contentArea').style.marginTop = '5px';
        document.querySelector('.mt_rightFeature').style.marginBottom = '0px';
    }
    //******** End of homepage toolbar fix code ********//
    //******** S14.0 - Start of Eventrelease code ********//

    /*
    *   Tries to grab a events release component
    */
    var eventsListRelease =  document.querySelector('.eventreleaseComponent');

    /*
    *   Check to see if there's a events release component present on the page
    */
    if(eventsListRelease != null){
        /*
        *   Variable initialization
        */
        var eventsReleaseStory = eventsListRelease.querySelector('.news_fullstory_story'); //Full story of the event. "Description" field
        var eventsReleaseDate = eventsListRelease.querySelector('.eventrelease_date'); //Date of the event. "From" and "to" fields
        var eventsReleaseTime = eventsListRelease.querySelector('.eventrelease_time'); //Time of th event. "From" and "to" fields
        var eventsReleaseWhere = eventsListRelease.querySelectorAll('.eventrelease_where'); //Who the event is open to. "Event Open To" field
        var eventsReleaseContact = eventsListRelease.querySelector('.eventrelease_contact'); //Who to contact ofr hte vent. "Contact" field
        var eventsReleaseLocation = eventsListRelease.querySelector('.eventrelease_location'); //Location of the event. "Location" field
        var eventsReleaseWebsite = eventsListRelease.querySelector('.eventrelease_website'); //Website for the event. "Website" field
        var eventsReleaseTitle = eventsListRelease.querySelector('.eventrelease_title'); //Title of the event. "Title" field
        var eventsReleaseDetailsList = eventsListRelease.querySelector('ul.details_list'); //Grabs the details list for the component

        /*
        *   Create a new element to use as title for the section "Website"
        *
        *   Makes it bolded, an inline elemeny with the text const as "Website"
        */
        var newText = document.createElement('p');
        newText.style.fontWeight = 'bold';
        newText.style.display = 'inline';
        newText.innerHTML = 'Website: ';

        //Generates the current list item link from the title

        eventsReleaseDetailsList.parentNode.insertBefore(eventsReleaseStory, eventsReleaseDetailsList.nextSibling);

        eventsReleaseStory.parentNode.appendChild(eventsReleaseContact).nextSibling;
        eventsReleaseStory.parentNode.appendChild(eventsReleaseWebsite).nextSibling;
        eventsReleaseWebsite.parentNode.insertBefore(newText, eventsReleaseWebsite);

        var eventsReleaseWhereTemp = '';
        eventsReleaseWhereTemp = eventsReleaseWhere[0].innerHTML;
        eventsReleaseWhere[0].innerHTML = '<b>Location:</b> ' + eventsReleaseWhereTemp;

        eventsReleaseWhereTemp = eventsReleaseWhere[1].innerHTML;
        eventsReleaseWhere[1].innerHTML = '<b>Open to:</b> ' + eventsReleaseWhereTemp;
    }
    //******** End of Eventrelease code ********//
});
