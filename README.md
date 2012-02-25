# RemoteHttpKernel
The RemoteHttpKernel is provided as an alternative to the standard HttpKernel in Symfony.  Rather than using the
local application, it processes the Request object via cURL, parsing the results into a proper Response object.

# Motivation
I was originally looking for a Symfony Bundle that provided an OO interface to the curl_* functions.  Along the 
way, I realized that I didn't actually want a cURL wrapper, I wanted something that could execute HTTP requests 
and return the result in a meaningful way.  So I decided to use the existing Symfony components and build a custom
HttpKernelInterprovided, relegating cURL to an implementation detail rather than the purpose of the project.
