/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/assets/core/js/custom/documentation/documentation.js":
/*!************************************************************************!*\
  !*** ./resources/assets/core/js/custom/documentation/documentation.js ***!
  \************************************************************************/
/***/ (() => {

eval("\n\nvar KTLayoutDocumentation = function () {\n  var menuWrapper;\n  var initInstance = function initInstance(element) {\n    var elements = element;\n    if (typeof elements === 'undefined') {\n      elements = document.querySelectorAll('.highlight');\n    }\n    if (elements && elements.length > 0) {\n      for (var i = 0; i < elements.length; ++i) {\n        var highlight = elements[i];\n        var copy = highlight.querySelector('.highlight-copy');\n        if (copy) {\n          var clipboard = new ClipboardJS(copy, {\n            target: function target(trigger) {\n              var highlight = trigger.closest('.highlight');\n              var el = highlight.querySelector('.tab-pane.active');\n              if (el == null) {\n                el = highlight.querySelector('.highlight-code');\n              }\n              return el;\n            }\n          });\n          clipboard.on('success', function (e) {\n            var caption = e.trigger.innerHTML;\n            e.trigger.innerHTML = 'copied';\n            e.clearSelection();\n            setTimeout(function () {\n              e.trigger.innerHTML = caption;\n            }, 2000);\n          });\n        }\n      }\n    }\n  };\n  var handleMenuScroll = function handleMenuScroll() {\n    var menuActiveItem = menuWrapper.querySelector(\".menu-link.active\");\n    if (!menuActiveItem) {\n      return;\n    }\n    if (KTUtil.isVisibleInContainer(menuActiveItem, menuWrapper) === true) {\n      return;\n    }\n    menuWrapper.scroll({\n      top: KTUtil.getRelativeTopPosition(menuActiveItem, menuWrapper),\n      behavior: 'smooth'\n    });\n  };\n  return {\n    init: function init(element) {\n      menuWrapper = document.querySelector('#kt_docs_aside_menu_wrapper');\n      initInstance(element);\n      if (menuWrapper) {\n        handleMenuScroll();\n      }\n    }\n  };\n}();\n\n// On document ready\nKTUtil.onDOMContentLoaded(function () {\n  KTLayoutDocumentation.init();\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9yZXNvdXJjZXMvYXNzZXRzL2NvcmUvanMvY3VzdG9tL2RvY3VtZW50YXRpb24vZG9jdW1lbnRhdGlvbi5qcy5qcyIsIm1hcHBpbmdzIjoiQUFBYTs7QUFFYixJQUFJQSxxQkFBcUIsR0FBRyxZQUFXO0VBQ25DLElBQUlDLFdBQVc7RUFFZixJQUFJQyxZQUFZLEdBQUcsU0FBZkEsWUFBWSxDQUFZQyxPQUFPLEVBQUU7SUFDakMsSUFBSUMsUUFBUSxHQUFHRCxPQUFPO0lBRXRCLElBQUssT0FBT0MsUUFBUSxLQUFLLFdBQVcsRUFBRztNQUNuQ0EsUUFBUSxHQUFHQyxRQUFRLENBQUNDLGdCQUFnQixDQUFDLFlBQVksQ0FBQztJQUN0RDtJQUVBLElBQUtGLFFBQVEsSUFBSUEsUUFBUSxDQUFDRyxNQUFNLEdBQUcsQ0FBQyxFQUFHO01BQ25DLEtBQU0sSUFBSUMsQ0FBQyxHQUFHLENBQUMsRUFBRUEsQ0FBQyxHQUFHSixRQUFRLENBQUNHLE1BQU0sRUFBRSxFQUFFQyxDQUFDLEVBQUc7UUFDeEMsSUFBSUMsU0FBUyxHQUFHTCxRQUFRLENBQUNJLENBQUMsQ0FBQztRQUMzQixJQUFJRSxJQUFJLEdBQUdELFNBQVMsQ0FBQ0UsYUFBYSxDQUFDLGlCQUFpQixDQUFDO1FBRXJELElBQUtELElBQUksRUFBRztVQUNSLElBQUlFLFNBQVMsR0FBRyxJQUFJQyxXQUFXLENBQUNILElBQUksRUFBRTtZQUNsQ0ksTUFBTSxFQUFFLGdCQUFTQyxPQUFPLEVBQUU7Y0FDdEIsSUFBSU4sU0FBUyxHQUFHTSxPQUFPLENBQUNDLE9BQU8sQ0FBQyxZQUFZLENBQUM7Y0FDN0MsSUFBSUMsRUFBRSxHQUFHUixTQUFTLENBQUNFLGFBQWEsQ0FBQyxrQkFBa0IsQ0FBQztjQUVwRCxJQUFLTSxFQUFFLElBQUksSUFBSSxFQUFHO2dCQUNkQSxFQUFFLEdBQUdSLFNBQVMsQ0FBQ0UsYUFBYSxDQUFDLGlCQUFpQixDQUFDO2NBQ25EO2NBRUEsT0FBT00sRUFBRTtZQUNiO1VBQ0osQ0FBQyxDQUFDO1VBRUZMLFNBQVMsQ0FBQ00sRUFBRSxDQUFDLFNBQVMsRUFBRSxVQUFTQyxDQUFDLEVBQUU7WUFDaEMsSUFBSUMsT0FBTyxHQUFHRCxDQUFDLENBQUNKLE9BQU8sQ0FBQ00sU0FBUztZQUVqQ0YsQ0FBQyxDQUFDSixPQUFPLENBQUNNLFNBQVMsR0FBRyxRQUFRO1lBQzlCRixDQUFDLENBQUNHLGNBQWMsRUFBRTtZQUVsQkMsVUFBVSxDQUFDLFlBQVc7Y0FDbEJKLENBQUMsQ0FBQ0osT0FBTyxDQUFDTSxTQUFTLEdBQUdELE9BQU87WUFDakMsQ0FBQyxFQUFFLElBQUksQ0FBQztVQUNaLENBQUMsQ0FBQztRQUNOO01BQ0o7SUFDSjtFQUNKLENBQUM7RUFFRCxJQUFJSSxnQkFBZ0IsR0FBRyxTQUFuQkEsZ0JBQWdCLEdBQWM7SUFDOUIsSUFBSUMsY0FBYyxHQUFHeEIsV0FBVyxDQUFDVSxhQUFhLENBQUMsbUJBQW1CLENBQUM7SUFFbkUsSUFBSyxDQUFDYyxjQUFjLEVBQUc7TUFDbkI7SUFDSjtJQUVBLElBQUtDLE1BQU0sQ0FBQ0Msb0JBQW9CLENBQUNGLGNBQWMsRUFBRXhCLFdBQVcsQ0FBQyxLQUFLLElBQUksRUFBRTtNQUNwRTtJQUNKO0lBRUFBLFdBQVcsQ0FBQzJCLE1BQU0sQ0FBQztNQUNmQyxHQUFHLEVBQUVILE1BQU0sQ0FBQ0ksc0JBQXNCLENBQUNMLGNBQWMsRUFBRXhCLFdBQVcsQ0FBQztNQUMvRDhCLFFBQVEsRUFBRTtJQUNkLENBQUMsQ0FBQztFQUNOLENBQUM7RUFFRCxPQUFPO0lBQ0hDLElBQUksRUFBRSxjQUFTN0IsT0FBTyxFQUFFO01BQ3BCRixXQUFXLEdBQUdJLFFBQVEsQ0FBQ00sYUFBYSxDQUFDLDZCQUE2QixDQUFDO01BRW5FVCxZQUFZLENBQUNDLE9BQU8sQ0FBQztNQUVyQixJQUFJRixXQUFXLEVBQUU7UUFDYnVCLGdCQUFnQixFQUFFO01BQ3RCO0lBQ0o7RUFDSixDQUFDO0FBQ0wsQ0FBQyxFQUFFOztBQUVIO0FBQ0FFLE1BQU0sQ0FBQ08sa0JBQWtCLENBQUMsWUFBVztFQUNqQ2pDLHFCQUFxQixDQUFDZ0MsSUFBSSxFQUFFO0FBQ2hDLENBQUMsQ0FBQyIsInNvdXJjZXMiOlsid2VicGFjazovLy8uL3Jlc291cmNlcy9hc3NldHMvY29yZS9qcy9jdXN0b20vZG9jdW1lbnRhdGlvbi9kb2N1bWVudGF0aW9uLmpzPzdlODIiXSwic291cmNlc0NvbnRlbnQiOlsiXCJ1c2Ugc3RyaWN0XCI7XHJcblxyXG52YXIgS1RMYXlvdXREb2N1bWVudGF0aW9uID0gZnVuY3Rpb24oKSB7XHJcbiAgICB2YXIgbWVudVdyYXBwZXI7XHJcblxyXG4gICAgdmFyIGluaXRJbnN0YW5jZSA9IGZ1bmN0aW9uKGVsZW1lbnQpIHtcclxuICAgICAgICB2YXIgZWxlbWVudHMgPSBlbGVtZW50O1xyXG5cclxuICAgICAgICBpZiAoIHR5cGVvZiBlbGVtZW50cyA9PT0gJ3VuZGVmaW5lZCcgKSB7XHJcbiAgICAgICAgICAgIGVsZW1lbnRzID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnLmhpZ2hsaWdodCcpO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgaWYgKCBlbGVtZW50cyAmJiBlbGVtZW50cy5sZW5ndGggPiAwICkge1xyXG4gICAgICAgICAgICBmb3IgKCB2YXIgaSA9IDA7IGkgPCBlbGVtZW50cy5sZW5ndGg7ICsraSApIHtcclxuICAgICAgICAgICAgICAgIHZhciBoaWdobGlnaHQgPSBlbGVtZW50c1tpXTtcclxuICAgICAgICAgICAgICAgIHZhciBjb3B5ID0gaGlnaGxpZ2h0LnF1ZXJ5U2VsZWN0b3IoJy5oaWdobGlnaHQtY29weScpO1xyXG5cclxuICAgICAgICAgICAgICAgIGlmICggY29weSApIHtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgY2xpcGJvYXJkID0gbmV3IENsaXBib2FyZEpTKGNvcHksIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdGFyZ2V0OiBmdW5jdGlvbih0cmlnZ2VyKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgaGlnaGxpZ2h0ID0gdHJpZ2dlci5jbG9zZXN0KCcuaGlnaGxpZ2h0Jyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgZWwgPSBoaWdobGlnaHQucXVlcnlTZWxlY3RvcignLnRhYi1wYW5lLmFjdGl2ZScpO1xyXG5cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmICggZWwgPT0gbnVsbCApIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBlbCA9IGhpZ2hsaWdodC5xdWVyeVNlbGVjdG9yKCcuaGlnaGxpZ2h0LWNvZGUnKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gZWw7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgICAgICAgICAgICAgY2xpcGJvYXJkLm9uKCdzdWNjZXNzJywgZnVuY3Rpb24oZSkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgY2FwdGlvbiA9IGUudHJpZ2dlci5pbm5lckhUTUw7XHJcblxyXG4gICAgICAgICAgICAgICAgICAgICAgICBlLnRyaWdnZXIuaW5uZXJIVE1MID0gJ2NvcGllZCc7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGUuY2xlYXJTZWxlY3Rpb24oKTtcclxuXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBlLnRyaWdnZXIuaW5uZXJIVE1MID0gY2FwdGlvbjtcclxuICAgICAgICAgICAgICAgICAgICAgICAgfSwgMjAwMCk7XHJcbiAgICAgICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICB9XHJcblxyXG4gICAgdmFyIGhhbmRsZU1lbnVTY3JvbGwgPSBmdW5jdGlvbigpIHtcclxuICAgICAgICB2YXIgbWVudUFjdGl2ZUl0ZW0gPSBtZW51V3JhcHBlci5xdWVyeVNlbGVjdG9yKFwiLm1lbnUtbGluay5hY3RpdmVcIik7XHJcblxyXG4gICAgICAgIGlmICggIW1lbnVBY3RpdmVJdGVtICkge1xyXG4gICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgfSBcclxuXHJcbiAgICAgICAgaWYgKCBLVFV0aWwuaXNWaXNpYmxlSW5Db250YWluZXIobWVudUFjdGl2ZUl0ZW0sIG1lbnVXcmFwcGVyKSA9PT0gdHJ1ZSkge1xyXG4gICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBtZW51V3JhcHBlci5zY3JvbGwoe1xyXG4gICAgICAgICAgICB0b3A6IEtUVXRpbC5nZXRSZWxhdGl2ZVRvcFBvc2l0aW9uKG1lbnVBY3RpdmVJdGVtLCBtZW51V3JhcHBlciksXHJcbiAgICAgICAgICAgIGJlaGF2aW9yOiAnc21vb3RoJ1xyXG4gICAgICAgIH0pO1xyXG4gICAgfVxyXG5cclxuICAgIHJldHVybiB7XHJcbiAgICAgICAgaW5pdDogZnVuY3Rpb24oZWxlbWVudCkge1xyXG4gICAgICAgICAgICBtZW51V3JhcHBlciA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNrdF9kb2NzX2FzaWRlX21lbnVfd3JhcHBlcicpO1xyXG5cclxuICAgICAgICAgICAgaW5pdEluc3RhbmNlKGVsZW1lbnQpO1xyXG5cclxuICAgICAgICAgICAgaWYgKG1lbnVXcmFwcGVyKSB7XHJcbiAgICAgICAgICAgICAgICBoYW5kbGVNZW51U2Nyb2xsKCk7XHJcbiAgICAgICAgICAgIH0gICAgICAgICAgICBcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG59KCk7XHJcblxyXG4vLyBPbiBkb2N1bWVudCByZWFkeVxyXG5LVFV0aWwub25ET01Db250ZW50TG9hZGVkKGZ1bmN0aW9uKCkge1xyXG4gICAgS1RMYXlvdXREb2N1bWVudGF0aW9uLmluaXQoKTtcclxufSk7Il0sIm5hbWVzIjpbIktUTGF5b3V0RG9jdW1lbnRhdGlvbiIsIm1lbnVXcmFwcGVyIiwiaW5pdEluc3RhbmNlIiwiZWxlbWVudCIsImVsZW1lbnRzIiwiZG9jdW1lbnQiLCJxdWVyeVNlbGVjdG9yQWxsIiwibGVuZ3RoIiwiaSIsImhpZ2hsaWdodCIsImNvcHkiLCJxdWVyeVNlbGVjdG9yIiwiY2xpcGJvYXJkIiwiQ2xpcGJvYXJkSlMiLCJ0YXJnZXQiLCJ0cmlnZ2VyIiwiY2xvc2VzdCIsImVsIiwib24iLCJlIiwiY2FwdGlvbiIsImlubmVySFRNTCIsImNsZWFyU2VsZWN0aW9uIiwic2V0VGltZW91dCIsImhhbmRsZU1lbnVTY3JvbGwiLCJtZW51QWN0aXZlSXRlbSIsIktUVXRpbCIsImlzVmlzaWJsZUluQ29udGFpbmVyIiwic2Nyb2xsIiwidG9wIiwiZ2V0UmVsYXRpdmVUb3BQb3NpdGlvbiIsImJlaGF2aW9yIiwiaW5pdCIsIm9uRE9NQ29udGVudExvYWRlZCJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./resources/assets/core/js/custom/documentation/documentation.js\n");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval-source-map devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./resources/assets/core/js/custom/documentation/documentation.js"]();
/******/ 	
/******/ })()
;