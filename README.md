# RemoteHttpKernelBundle
The RemoteHttpKernelBundle is provided as an alternative to the HttpKernel.  It processes the Request Object
via cURL, building a Response Object from the results of the cURL request.

# Motivation
I was originally looking for a Symfony Bundle that provided an OO interface to the curl_* functions.  Along the 
way, I realized that I didn't actually want a cURL wrapper, I wanted something that could execute HTTP requests 
and return the result in a meaningful way.  So I decided to build on top of the existing HttpFoundation provided
in Symfony, relegating cURL to an implementation detail rather than the primary focus.
