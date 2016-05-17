<article class="main-info">
    <header>
        <h1>Looking for some data?</h1>
    </header>
    <section>
        <div class="row-block">
            <div class="col-size-12">
                Open Data System will help you to find interesting information to use them for your company or for
                research purposes. Our database contains more than 10,000 data sets under hundreds of categories. <?php if (!$this->roleAccess->isLoggedIn()) : ?>
                Please register and have fun!<?php endif; ?>
            </div>
        </div>
        <div class="row-block">
            <br>
            <form action="/site/search" method="GET">
                <div class="col-size-9">
                    <input type="text" placeholder="Enter your search criteria" name="searchtext" required>
                    <input type="hidden" name="tags">
                </div>
                <div class="col-size-3">
                    <input type="submit" value="Let's begin!" class="btn btn-success">
                </div>
            </form>
            <br>
            <br>
        </div>
    </section>
</article>