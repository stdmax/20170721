<!DOCTYPE html>
<html>
    <head>
        <base href="://www.xiag.ch/" />
        <link href="/files/css/testtask-styles.css?data=26.07.2012" rel="stylesheet" />
        <meta charset="utf-8" />
        <title>XIAG test task</title>
        <meta name="robots" content="noindex,nofollow" />
        <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0" />
    </head>
    <body>
    <div class="content">
        <header>URL shortener</header>
        <form action="javascript:generateShortUrl()">
            <table>
                <tr>
                    <th>Long URL</th>
                    <th>Short URL</th>
                </tr>
                <tr>
                    <td>
                        <input type="url" name="url">
                        <input type="submit" value="Do!">
                    </td>
                    <td id=result></td>
                </tr>
            </table>
        </form>
        <footer>
            <pre>
            Using this HTML please implement the following:

            1. Site-visitor (V) enters any original URL to the Input field, like
            http://anydomain/any/path/etc;
            2. V clicks submit button;
            3. Page makes AJAX-request;
            4. Short URL appears in Span element, like http://yourdomain/abCdE (don't use any
               external APIs as goo.gl etc.);
            5. V can copy short URL and repeat process with another link

            Short URL should redirect to the original link in any browser from any place and keep
            actuality forever, doesn't matter how many times application has been used after that.


            Requirements:

            1. Use PHP or Node.js;
            2. Don't use any frameworks.
                
            Expected result:

            1. Source code;
            2. System requirements and installation instructions on our platform, in English.
            </pre>

        </footer>
    </div>


	<script>
		function generateShortUrl() {
			var output = document.getElementById('result'),
				url = document.forms[0].elements.url.value,
				xhr = new XMLHttpRequest();

			output.innerHTML = '...';
			xhr.onreadystatechange = function() {
				if (4 == this.readyState
					&& 200 == this.status) {
					data = JSON.parse(this.responseText);
					if (data.error) {
						output.innerHTML = data.error;
					}
					if (data.shortUrl) {
						output.innerHTML = '<span>' + data.shortUrl + '</span>';
					}
				}
			};
			xhr.open('GET', '<?php echo $base_url; ?>generate?url=' + encodeURIComponent(url), false);
			xhr.send();
		}
	</script>


    </body>
</html>
