(function (window, $)
{
    window._EPYT_ = window._EPYT_ || {
        ajaxurl: "\/wp-admin\/admin-ajax.php",
        security: "",
        gallery_scrolloffset: 100,
        eppathtoscripts: "\/wp-content\/plugins\/youtube-embed-plus\/scripts\/",
        eppath: "\/wp-content\/plugins\/youtube-embed-plus\/",
        epresponsiveselector: ["iframe.__youtube_prefs_widget__"],
        epdovol: true,
        evselector: 'iframe.__youtube_prefs__[src], iframe[src*="youtube.com/embed/"], iframe[src*="youtube-nocookie.com/embed/"]',
        stopMobileBuffer: true,
        ajax_compat: false,
        usingdefault: true,
        ytapi_load: 'light',
        pause_others: false,
        facade_mode: false,
        not_live_on_channel: false,
        maxres_facade: 'eager'
    };

    window._EPYT_.touchmoved = false;

    window._EPYT_.apiVideos = window._EPYT_.apiVideos || {};

    if (window.location.toString().indexOf('https://') === 0)
    {
        window._EPYT_.ajaxurl = window._EPYT_.ajaxurl.replace("http://", "https://");
    }

    window._EPYT_.pageLoaded = false;
    $(window).on('load._EPYT_', function ()
    {
        window._EPYT_.pageLoaded = true;
    });

    if (!document.querySelectorAll)
    {
        document.querySelectorAll = function (selector)
        {
            var doc = document, head = doc.documentElement.firstChild, styleTag = doc.createElement('STYLE');
            head.appendChild(styleTag);
            doc.__qsaels = [];
            styleTag.styleSheet.cssText = selector + "{x:expression(document.__qsaels.push(this))}";
            window.scrollBy(0, 0);
            return doc.__qsaels;
        };
    }

    if (typeof window._EPADashboard_ === 'undefined')
    {
        window._EPADashboard_ =
                {
                    initStarted: false,
                    checkCount: 0,
                    onPlayerReady: function (event)
                    {
                        try
                        {
                            if (typeof _EPYT_.epdovol !== "undefined" && _EPYT_.epdovol)
                            {
                                var vol = parseInt(event.target.getIframe().getAttribute("data-vol"));
                                if (!isNaN(vol))
                                {
                                    if (vol === 0)
                                    {
                                        event.target.mute();
                                    }
                                    else
                                    {
                                        if (event.target.isMuted())
                                        {
                                            event.target.unMute();
                                        }
                                        event.target.setVolume(vol);
                                    }
                                }
                            }

                            var epautoplay = parseInt(event.target.getIframe().getAttribute("data-epautoplay"));
                            if (!isNaN(epautoplay) && epautoplay === 1)
                            {
                                event.target.playVideo();
                            }

                        }
                        catch (err)
                        {
                        }

                        try
                        {
                            var apiVideoIframe = event.target.getIframe();
                            var apiVideoId = apiVideoIframe.getAttribute("id");
                            window._EPYT_.apiVideos[apiVideoId] = event.target;

                            if (window._EPYT_.not_live_on_channel && event.target.getVideoUrl().indexOf('live_stream') > 0)
                            {
                                window._EPADashboard_.doLiveFallback(apiVideoIframe);
                            }
                        }
                        catch (liveErr)
                        {
                        }
                        finally
                        {
                            $(event.target.getIframe()).css('opacity', 1);
                        }
                    },
                    onPlayerStateChange: function (event)
                    {
                        var ifm = event.target.getIframe();

                        if (window._EPYT_.pause_others && event.data === window.YT.PlayerState.PLAYING)
                        {
                            window._EPADashboard_.pauseOthers(event.target);
                        }

                        if (event.data === window.YT.PlayerState.PLAYING && event.target.ponce !== true && ifm.src.indexOf('autoplay=1') === -1)
                        {
                            event.target.ponce = true;
                        }

                        if (event.data === window.YT.PlayerState.ENDED && $(ifm).data('relstop') == '1')
                        {
                            if (typeof event.target.stopVideo === 'function')
                            {
                                event.target.stopVideo();
                            }
                            else
                            {
                                var $iframeTemp = $(ifm).clone(true).off();
                                $iframeTemp.attr('src', window._EPADashboard_.cleanSrc($iframeTemp.attr('src').replace('autoplay=1', 'autoplay=0')));
                                $(ifm).replaceWith($iframeTemp);
                                window._EPADashboard_.setupevents($iframeTemp.attr('id'));
                                ifm = $iframeTemp.get(0);
                            }
                        }

                        var $gallery = $(ifm).closest('.epyt-gallery');
                        if (!$gallery.length)
                        {
                            $gallery = $('#' + $(ifm).data('epytgalleryid'));
                        }
                        if ($gallery.length)
                        {
                            var autonext = $gallery.find('.epyt-pagebutton').first().data('autonext') == '1';
                            if (autonext && event.data === window.YT.PlayerState.ENDED)
                            {
                                var $currvid = $gallery.find('.epyt-current-video');
                                if (!$currvid.length)
                                {
                                    $currvid = $gallery.find('.epyt-gallery-thumb').first();
                                }
                                var $nextvid = $currvid.find(' ~ .epyt-gallery-thumb').first();

                                if ($nextvid.length)
                                {
                                    $nextvid.trigger('click');
                                }
                                else
                                {
                                    $gallery.find('.epyt-pagebutton.epyt-next[data-pagetoken!=""][data-pagetoken]').first().trigger('click');

                                }
                            }
                        }

                    },
                    isMobile: function ()
                    {
                        return /Mobi|Android/i.test(navigator.userAgent);
                    },
                    base64DecodeUnicode: function (str)
                    {
                        str = str.replace(/\s/g, '');
                        return decodeURIComponent(Array.prototype.map.call(atob(str), function (c)
                        {
                            return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
                        }).join(''))
                    },
                    findSwapBlock: function (playerIframe)
                    {
                        var $swapBlock = $(playerIframe).closest('.wp-block-embed');
                        if (!$swapBlock.length)
                        {
                            $swapBlock = $(playerIframe).closest('.epyt-live-chat-wrapper');
                        }
                        if (!$swapBlock.length)
                        {
                            $swapBlock = $(playerIframe).closest('.epyt-video-wrapper');
                        }
                        if (!$swapBlock.length)
                        {
                            $swapBlock = $(playerIframe);
                        }
                        return $swapBlock;
                    },
                    doLiveFallback: function (playerIframe)
                    {
                        var $swapBlock = _EPADashboard_.findSwapBlock(playerIframe);

                        if ($swapBlock.length)
                        {
                            var $liveFallbackBlock = $('#epyt-live-fallback');
                            if ($liveFallbackBlock.length)
                            {
                                var fallbackHtml = '';
                                try
                                {
                                    fallbackHtml = window._EPADashboard_.base64DecodeUnicode($liveFallbackBlock.get(0).innerHTML);
                                }
                                catch (fallbackErr)
                                {
                                }
                                if (fallbackHtml)
                                {
                                    var $swapBlockParent = $swapBlock.parent();
                                    window._EPADashboard_.loadYTAPI();
                                    $swapBlock.replaceWith(fallbackHtml);
                                    window._EPADashboard_.apiInit();
                                    window._EPADashboard_.pageReady();
                                    setTimeout(function ()
                                    {
                                        if (typeof $.fn.fitVidsEP !== 'undefined')
                                        {
                                            $swapBlockParent.fitVidsEP();
                                        }
                                    }, 1);
                                }
                            }
                        }
                    },
                    videoEqual: function (a, b)
                    {
                        if (a.getIframe && b.getIframe && a.getIframe().id === b.getIframe().id)
                        {
                            return true;
                        }
                        return false;
                    },
                    pauseOthers: function (currentVid)
                    {
                        if (!currentVid)
                        {
                            return;
                        }
                        for (var vidKey in window._EPYT_.apiVideos)
                        {
                            var vid = window._EPYT_.apiVideos[vidKey];
                            if (
                                    vid &&
                                    typeof vid.pauseVideo === 'function' &&
                                    vid != currentVid &&
                                    !_EPADashboard_.videoEqual(vid, currentVid) &&
                                    typeof vid.getPlayerState === 'function' &&
                                    [YT.PlayerState.BUFFERING, window.YT.PlayerState.PLAYING].indexOf(vid.getPlayerState()) >= 0
                                    )
                            {
                                vid.pauseVideo();
                            }
                        }
                    },
                    justid: function (s)
                    {
                        return new RegExp("[\\?&]v=([^&#]*)").exec(s)[1];
                    },
                    setupevents: function (iframeid)
                    {
                        if (typeof (window.YT) !== 'undefined' && window.YT !== null && window.YT.loaded)
                        {
                            var thisvid = document.getElementById(iframeid);

                            if (!thisvid.epytsetupdone)
                            {
                                window._EPADashboard_.log('Setting up YT API events: ' + iframeid);
                                thisvid.epytsetupdone = true;
                                var ytOptions = {
                                    events: {
                                        "onReady": window._EPADashboard_.onPlayerReady,
                                        "onStateChange": window._EPADashboard_.onPlayerStateChange
                                    },
                                    host: (thisvid.src || '').indexOf('nocookie') > 0 ? 'https://www.youtube-nocookie.com' : 'https://www.youtube.com'
                                };
                                return new window.YT.Player(iframeid, ytOptions);
                            }
                        }
                    },
                    apiInit: function ()
                    {
                        if (typeof (window.YT) !== 'undefined')
                        {
                            window._EPADashboard_.initStarted = true;
                            var __allytifr = document.querySelectorAll(_EPYT_.evselector);
                            for (var i = 0; i < __allytifr.length; i++)
                            {
                                if (!__allytifr[i].hasAttribute("id"))
                                {
                                    __allytifr[i].id = "_dytid_" + Math.round(Math.random() * 8999 + 1000);
                                }
                                window._EPADashboard_.setupevents(__allytifr[i].id);
                            }
                        }
                    },
                    log: function (msg)
                    {
                        try
                        {
                            console.log(msg);
                        }
                        catch (err)
                        {
                        }
                    },
                    doubleCheck: function ()
                    {
                        window._EPADashboard_.checkInterval = setInterval(function ()
                        {
                            window._EPADashboard_.checkCount++;
                            if (window._EPADashboard_.checkCount >= 5 || window._EPADashboard_.initStarted)
                            {
                                clearInterval(window._EPADashboard_.checkInterval);
                            }
                            else
                            {
                                window._EPADashboard_.apiInit();
                                window._EPADashboard_.log('YT API init check');
                            }

                        }, 1000);
                    },
                    selectText: function (ele)
                    {
                        if (document.selection)
                        {
                            var range = document.body.createTextRange();
                            range.moveToElementText(ele);
                            range.select();
                        }
                        else if (window.getSelection)
                        {
                            var selection = window.getSelection();
                            var range = document.createRange();
                            range.selectNode(ele);
                            selection.removeAllRanges();
                            selection.addRange(range);
                        }
                    },
                    setVidSrc: function ($iframe, vidSrc)
                    {
                        if ($iframe.is('.epyt-facade'))
                        {
                            $iframe.attr('data-facadesrc', window._EPADashboard_.cleanSrc(vidSrc));
                            $iframe.trigger('click');
                            return;
                        }

                        var cleanSrcValue = window._EPADashboard_.cleanSrc(vidSrc);
                        var parsed = window._EPADashboard_.parseYouTubeEmbedUrl(cleanSrcValue);

                        var iframeId = $iframe.attr('id');
                        var player = iframeId ? window._EPYT_.apiVideos[iframeId] : null;

                        var ytReady = typeof window.YT !== 'undefined' && window.YT !== null && window.YT.loaded;

                        if (!parsed.videoId || parsed.videoId === 'live_stream' || !ytReady || !player || typeof player.loadVideoById !== 'function')
                        {
                            window._EPADashboard_.setVidSrcLegacy($iframe, cleanSrcValue);
                            return;
                        }

                        var loadOptions = window._EPADashboard_.mapUrlParamsToLoadOptions(parsed.params, parsed.videoId);
                        var shouldAutoplay = parsed.params.autoplay === '1';

                        try
                        {
                            if (shouldAutoplay)
                            {
                                player.loadVideoById(loadOptions);
                            }
                            else
                            {
                                player.cueVideoById(loadOptions);
                            }

                            $iframe.data('ep-current-src', cleanSrcValue);
                        }
                        catch (apiError)
                        {
                            window._EPADashboard_.setVidSrcLegacy($iframe, cleanSrcValue);
                            return;
                        }

                        $iframe.css('opacity', '1');
                    },
                    cleanSrc: function (srcInput)
                    {
                        var cleanedUrl = srcInput.replace('enablejsapi=1?enablejsapi=1', 'enablejsapi=1');
                        return cleanedUrl;
                    },
                    parseYouTubeEmbedUrl: function (embedUrl)
                    {
                        var result = {
                            videoId: null,
                            params: {}
                        };

                        if (!embedUrl || typeof embedUrl !== 'string')
                        {
                            return result;
                        }

                        var videoIdMatch = embedUrl.match(/\/embed\/([a-zA-Z0-9_-]{11})/);
                        if (videoIdMatch && videoIdMatch[1])
                        {
                            result.videoId = videoIdMatch[1];
                        }
                        else if (embedUrl.indexOf('/embed/live_stream') > -1)
                        {
                            result.videoId = 'live_stream';
                        }

                        var queryStart = embedUrl.indexOf('?');
                        if (queryStart > -1)
                        {
                            var queryString = embedUrl.substring(queryStart + 1).split('#')[0];
                            var pairs = queryString.split('&');
                            for (var i = 0; i < pairs.length; i++)
                            {
                                var pair = pairs[i].split('=');
                                if (pair.length === 2)
                                {
                                    result.params[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
                                }
                            }
                        }

                        return result;
                    },
                    mapUrlParamsToLoadOptions: function (urlParams, videoId)
                    {
                        var options = {
                            videoId: videoId
                        };

                        var startVal = urlParams.start || urlParams.t;
                        if (startVal)
                        {
                            var startSeconds = parseInt(startVal, 10);
                            if (!isNaN(startSeconds) && startSeconds > 0)
                            {
                                options.startSeconds = startSeconds;
                            }
                        }

                        if (urlParams.end)
                        {
                            var endSeconds = parseInt(urlParams.end, 10);
                            if (!isNaN(endSeconds) && endSeconds > 0)
                            {
                                options.endSeconds = endSeconds;
                            }
                        }

                        return options;
                    },
                    setVidSrcLegacy: function ($iframe, cleanSrcValue)
                    {
                        if ($iframe.get(0).src && $iframe.get(0).contentWindow && $iframe.get(0).contentWindow.location)
                        {
                            try
                            {
                                $iframe.get(0).contentWindow.location.replace(cleanSrcValue);
                            }
                            catch (err)
                            {
                                $iframe.attr('src', cleanSrcValue);
                            }
                        }
                        else
                        {
                            $iframe.attr('src', cleanSrcValue);
                        }
                        $iframe.get(0).epytsetupdone = false;
                        // Ensure the iframe has an ID before calling setupevents
                        if (!$iframe.attr('id'))
                        {
                            $iframe.attr('id', "_dytid_" + Math.round(Math.random() * 8999 + 1000));
                        }
                        window._EPADashboard_.setupevents($iframe.attr('id'));
                        $iframe.css('opacity', '1');
                    },
                    loadYTAPI: function ()
                    {
                        if (typeof window.YT === 'undefined')
                        {
                            if (window._EPYT_.ytapi_load !== 'never' && (window._EPYT_.ytapi_load === 'always' || $('iframe[src*="youtube.com/embed/"], iframe[data-src*="youtube.com/embed/"], .__youtube_prefs__').length))
                            {
                                var iapi = document.createElement('script');
                                iapi.src = "https://www.youtube.com/iframe_api";
                                iapi.type = "text/javascript";
                                document.getElementsByTagName('head')[0].appendChild(iapi);
                            }
                        }
                        else if (window.YT.loaded)
                        {
                            if (window._EPYT_.pageLoaded)
                            {
                                window._EPADashboard_.apiInit();
                                window._EPADashboard_.log('YT API available');
                            }
                            else
                            {
                                $(window).on('load._EPYT_', function ()
                                {
                                    window._EPADashboard_.apiInit();
                                    window._EPADashboard_.log('YT API available 2');
                                });
                            }
                        }
                    },
                    resolveFacadeQuality: function (img, isError)
                    {
                        img.epytFacadeCount = typeof (img.epytFacadeCount) === 'undefined' ? 0 : img.epytFacadeCount + 1;
                        if (isError || img.naturalHeight < 200)
                        {
                            var facadeOldSrc = $(img).attr("src");
                            if (facadeOldSrc)
                            {
                                $(img).attr("src", facadeOldSrc.replace('maxresdefault', 'hqdefault'));
                                $(img).off('load.epyt');
                            }
                        }
                        if (img.epytFacadeCount > 2)
                        {
                            $(img).off('load.epyt');
                        }
                    },
                    maximizeFacadeQuality: function (img)
                    {
                        var facadeOldSrc = $(img).attr("src");
                        if (facadeOldSrc && facadeOldSrc.indexOf('maxresdefault') < 0)
                        {
                            var maxResSrc = facadeOldSrc.replace('hqdefault', 'maxresdefault');
                            var maxRes = new Image();
                            maxRes.src = maxResSrc;
                            $(maxRes).on("load.epyt", function ()
                            {
                                $(maxRes).off('load.epyt');
                                if (maxRes.naturalHeight > 200)
                                {
                                    $(img).off('load.epyt');
                                    $(img).attr("src", maxRes.src);
                                }
                            }).on('error', function ()
                            {
                                $(maxRes).off('load.epyt');
                            }).each(function ()
                            {
                                if (maxRes.complete)
                                {
                                    $(maxRes).trigger('load');
                                }
                            });
                        }
                    },
                    pageReady: function ()
                    {
                        if (window._EPYT_.not_live_on_channel && window._EPYT_.ytapi_load !== 'never')
                        {
                            $('.epyt-live-channel').each(function ()
                            {
                                var $ch = $(this);
                                if (!$ch.data('eypt-fallback'))
                                {
                                    $ch.data('eypt-fallback', true);
                                    $ch.css('opacity', 0);
                                    setTimeout(function ()
                                    {
                                        $ch.css('opacity', 1);
                                    }, 4000);
                                }
                            });
                        }
                        $('.epyt-gallery').each(function ()
                        {
                            var $container = $(this);
                            if (!$container.data('epytevents') || !$('body').hasClass('block-editor-page'))
                            {
                                $container.data('epytevents', '1');
                                var $iframe = $(this).find('iframe, div.__youtube_prefs_gdpr__, div.epyt-facade').first();

                                var initSrc = $iframe.data('src') || $iframe.data('facadesrc') || $iframe.attr('src');
                                if (!initSrc)
                                {
                                    initSrc = $iframe.data('ep-src');
                                }
                                var firstId = $(this).find('.epyt-gallery-list .epyt-gallery-thumb').first().data('videoid');
                                if (typeof (initSrc) !== 'undefined')
                                {
                                    initSrc = initSrc.replace(firstId, 'GALLERYVIDEOID');
                                    $container.data('ep-gallerysrc', initSrc);
                                }
                                else if ($iframe.hasClass('__youtube_prefs_gdpr__'))
                                {
                                    $container.data('ep-gallerysrc', '');
                                }
                                $container.on('click touchend', '.epyt-gallery-list .epyt-gallery-thumb', function (e)
                                {
                                    $iframe = $container.find('iframe, div.__youtube_prefs_gdpr__, div.epyt-facade').first();
                                    if (window._EPYT_.touchmoved)
                                    {
                                        return;
                                    }
                                    if (!$(this).hasClass('epyt-current-video'))
                                    {
                                        $container.find('.epyt-gallery-list .epyt-gallery-thumb').removeClass('epyt-current-video');
                                        $(this).addClass('epyt-current-video');
                                        var vid = $(this).data('videoid');
                                        $container.data('currvid', vid);
                                        var vidSrc = $container.data('ep-gallerysrc').replace('GALLERYVIDEOID', vid);

                                        var thumbplay = $container.find('.epyt-pagebutton').first().data('thumbplay');
                                        if (thumbplay !== '0' && thumbplay !== 0)
                                        {
                                            if (vidSrc.indexOf('autoplay') > 0)
                                            {
                                                vidSrc = vidSrc.replace('autoplay=0', 'autoplay=1');
                                            }
                                            else
                                            {
                                                vidSrc += '&autoplay=1';
                                            }

                                            $iframe.addClass('epyt-thumbplay');
                                        }

                                        // https://github.com/jquery/jquery-ui/blob/master/ui/scroll-parent.js
                                        var bodyScrollTop = Math.max($('body').scrollTop(), $('html').scrollTop());
                                        var scrollNext = $iframe.offset().top - parseInt(_EPYT_.gallery_scrolloffset);
                                        if (bodyScrollTop > scrollNext)
                                        {
                                            $('html, body').animate({
                                                scrollTop: scrollNext
                                            }, 500, function ()
                                            {
                                                window._EPADashboard_.setVidSrc($iframe, vidSrc);
                                            });
                                        }
                                        else
                                        {
                                            window._EPADashboard_.setVidSrc($iframe, vidSrc);
                                        }
                                    }

                                }).on('touchmove', function (e)
                                {
                                    window._EPYT_.touchmoved = true;
                                }).on('touchstart', function ()
                                {
                                    window._EPYT_.touchmoved = false;
                                }).on('keydown', '.epyt-gallery-list .epyt-gallery-thumb, .epyt-pagebutton', function (e)
                                {
                                    var code = e.which;
                                    if ((code === 13) || (code === 32))
                                    {
                                        e.preventDefault();
                                        $(this).trigger('click');

                                    }
                                });

                                $container.on('mouseenter', '.epyt-gallery-list .epyt-gallery-thumb', function ()
                                {
                                    $(this).addClass('hover');
                                });

                                $container.on('mouseleave', '.epyt-gallery-list .epyt-gallery-thumb', function ()
                                {
                                    $(this).removeClass('hover');
                                });

                                $container.on('click touchend', '.epyt-pagebutton', function (ev)
                                {
                                    if (window._EPYT_.touchmoved)
                                    {
                                        return;
                                    }
                                    if (!$container.find('.epyt-gallery-list').hasClass('epyt-loading'))
                                    {
                                        $container.find('.epyt-gallery-list').addClass('epyt-loading');
                                        var humanClick = typeof (ev.originalEvent) !== 'undefined';
                                        var pageData = {
                                            action: 'my_embedplus_gallery_page',
                                            security: _EPYT_.security,
                                            options: {
                                                playlistId: $(this).data('playlistid'),
                                                pageToken: $(this).data('pagetoken'),
                                                pageSize: $(this).data('pagesize'),
                                                columns: $(this).data('epcolumns'),
                                                showTitle: $(this).data('showtitle'),
                                                showPaging: $(this).data('showpaging'),
                                                autonext: $(this).data('autonext'),
                                                thumbplay: $(this).data('thumbplay')
                                            }
                                        };

                                        var forward = $(this).hasClass('epyt-next');
                                        var currpage = parseInt($container.data('currpage') + "");
                                        currpage += forward ? 1 : -1;
                                        $container.data('currpage', currpage);

                                        $.post(_EPYT_.ajaxurl, pageData, function (response)
                                        {
                                            $container.find('.epyt-gallery-list').html(response);
                                            $container.find('.epyt-current').each(function ()
                                            {
                                                $(this).text($container.data('currpage'));
                                            });
                                            $container.find('.epyt-gallery-thumb[data-videoid="' + $container.data('currvid') + '"]').addClass('epyt-current-video');

                                            if ($container.find('.epyt-pagebutton').first().data('autonext') == '1' && !humanClick)
                                            {
                                                $container.find('.epyt-gallery-thumb').first().trigger('click');
                                            }

                                        })
                                                .fail(function ()
                                                {
                                                    alert('Sorry, there was an error loading the next page.');
                                                })
                                                .always(function ()
                                                {
                                                    $container.find('.epyt-gallery-list').removeClass('epyt-loading');

                                                    if ($container.find('.epyt-pagebutton').first().data('autonext') != '1')
                                                    {
                                                        // https://github.com/jquery/jquery-ui/blob/master/ui/scroll-parent.js
                                                        var bodyScrollTop = Math.max($('body').scrollTop(), $('html').scrollTop());
                                                        var scrollNext = $container.find('.epyt-gallery-list').offset().top - parseInt(_EPYT_.gallery_scrolloffset);
                                                        if (bodyScrollTop > scrollNext)
                                                        {
                                                            $('html, body').animate({
                                                                scrollTop: scrollNext
                                                            }, 500);
                                                        }
                                                    }

                                                });
                                    }
                                }).on('touchmove', function (e)
                                {
                                    window._EPYT_.touchmoved = true;
                                }).on('touchstart', function ()
                                {
                                    window._EPYT_.touchmoved = false;
                                });
                            }
                        });

                        $('.__youtube_prefs_gdpr__.epyt-is-override').each(function ()
                        {
                            $(this).parent('.wp-block-embed__wrapper').addClass('epyt-is-override__wrapper');
                        });

                        $('button.__youtube_prefs_gdpr__').on('click', function (e)
                        {
                            e.preventDefault();
                            if ($.cookie)
                            {
                                $.cookie("ytprefs_gdpr_consent", '1', {expires: 30, path: '/'});
                                window.top.location.reload();
                            }
                        });

                        if (window._EPYT_.maxres_facade === 'eager')
                        {
                            $('img.epyt-facade-poster').on("load.epyt", function ()
                            {
                                window._EPADashboard_.resolveFacadeQuality(this, false);
                            }).on('error', function ()
                            {
                                window._EPADashboard_.resolveFacadeQuality(this, true);
                            }).each(function ()
                            {
                                if (this.complete)
                                {
                                    $(this).trigger('load');
                                }
                            });
                        }
                        else if (window._EPYT_.maxres_facade === 'soft')
                        {

                            $('img.epyt-facade-poster').on("load.epyt", function ()
                            {
                                window._EPADashboard_.maximizeFacadeQuality(this);
                            }).each(function ()
                            {
                                if (this.complete)
                                {
                                    $(this).trigger('load');
                                }
                            });
                        }

                        $('.epyt-facade-play').each(function ()
                        {
                            if (!$(this).find('svg').length)
                            {
                                $(this).append('<svg data-no-lazy="1" height="100%" version="1.1" viewBox="0 0 68 48" width="100%"><path class="ytp-large-play-button-bg" d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55 C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19 C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24S67.94,13.05,66.52,7.74z" fill="#f00"></path><path d="M 45,24 27,14 27,34" fill="#fff"></path></svg>');
                            }
                        });

                        $('.epyt-facade-poster[data-facadeoembed]').each(function ()
                        {
                            var $facadePoster = $(this);
                            if (!$facadePoster.data('facadeoembedcomplete'))
                            {
                                $facadePoster.data('facadeoembedcomplete', '1');
                                var facadeOembedUrl = 'https://www.youtube.com/' + $facadePoster.data('facadeoembed');
                                $.get('https://youtube.com/oembed', {url: facadeOembedUrl, format: 'json'},
                                        function (response)
                                        {
                                            var newSrc = window._EPYT_.maxres_facade === 'eager' ? response.thumbnail_url.replace('hqdefault', 'maxresdefault') : response.thumbnail_url;
                                            $facadePoster.attr('src', newSrc);
                                        }, 'json')
                                        .fail(function ()
                                        {
                                        })
                                        .always(function ()
                                        {
                                        });
                            }
                        });

                        $(document).on('click', '.epyt-facade', function (e)
                        {
                            var $facade = $(this);
                            var srcTemp = $facade.attr('data-facadesrc');
                            srcTemp = window._EPADashboard_.cleanSrc(srcTemp);
                            var iframe = document.createElement('iframe');
                            for (var i = 0; i < this.attributes.length; i++)
                            {
                                var attrib = this.attributes[i];
                                if (['allow', 'class', 'height', 'id', 'width'].indexOf(attrib.name.toLowerCase()) >= 0 || attrib.name.toLowerCase().indexOf('data-') == 0)
                                {
                                    $(iframe).attr(attrib.name, attrib.value);
                                }
                            }
                            $(iframe).removeClass('epyt-facade');
                            $(iframe).attr('allowfullscreen', '')
                            .attr('title', $facade.find('img').attr('alt'))
                            .attr('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share')
                            .attr('referrerpolicy', 'strict-origin-when-cross-origin');

                            window._EPADashboard_.loadYTAPI();
                            $facade.replaceWith(iframe);
                            window._EPADashboard_.setVidSrc($(iframe), srcTemp);
                            setTimeout(function ()
                            {
                                if (typeof $.fn.fitVidsEP !== 'undefined')
                                {
                                    $($(iframe).parent()).fitVidsEP();
                                }
                            }, 1);
                        });
                    }
                };
    }

    window.onYouTubeIframeAPIReady = typeof window.onYouTubeIframeAPIReady !== 'undefined' ? window.onYouTubeIframeAPIReady : function ()
    {
        if (window._EPYT_.pageLoaded)
        {
            window._EPADashboard_.apiInit();
            window._EPADashboard_.log('YT API ready');
        }
        else
        {
            $(window).on('load._EPYT_', function ()
            {
                window._EPADashboard_.apiInit();
                window._EPADashboard_.log('YT API ready 2');
            });
        }
    };

    if (!window._EPYT_.facade_mode || (window._EPYT_.not_live_on_channel && $('iframe[src*="youtube.com/embed/live_stream"], iframe[data-src*="youtube.com/embed/live_stream"]').length))
    {
        window._EPADashboard_.loadYTAPI();
    }

    if (window._EPYT_.pageLoaded)
    {
        window._EPADashboard_.doubleCheck();
    }
    else
    {
        $(window).on('load._EPYT_', function ()
        {
            window._EPADashboard_.doubleCheck();
        });
    }


    $(document).ready(function ()
    {
        window._EPADashboard_.pageReady();

        if (!window._EPYT_.facade_mode || (window._EPYT_.not_live_on_channel && $('iframe[src*="youtube.com/embed/live_stream"], iframe[data-src*="youtube.com/embed/live_stream"]').length))
        {
            window._EPADashboard_.loadYTAPI();
        }

        if (window._EPYT_.ajax_compat)
        {
            $(window).on('load._EPYT_', function ()
            {
                $(document).ajaxSuccess(function (e, xhr, settings)
                {
                    if (xhr && xhr.responseText && (xhr.responseText.indexOf('<iframe ') !== -1 || xhr.responseText.indexOf('enablejsapi') !== -1))
                    {
                        window._EPADashboard_.loadYTAPI();
                        window._EPADashboard_.apiInit();
                        window._EPADashboard_.log('YT API AJAX');
                        window._EPADashboard_.pageReady();
                    }
                });
            });
        }

    });
})(window, jQuery);
